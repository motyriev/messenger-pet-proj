<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Transformers\FriendRequestTransformer;
use App\Transformers\FriendTransformer;
use FriendsService\FriendsServiceClient;
use FriendsService\GetFriendRequestsByUserIdRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Grpc\ChannelCredentials;
use Illuminate\Support\Facades\Log;
use App\Exceptions\DashboardException;
use JetBrains\PhpStorm\ArrayShape;
use Motyriev\MyDTOLibrary\UserPairDTO;
use Psr\Http\Message\ResponseInterface;

class DashboardService
{
    private FriendsServiceClient $friendsServiceClient;
    private Client $httpClient;

    public function __construct(\FriendsService\FriendsServiceClient $friendsServiceClient, Client $httpClient)
    {
        $this->friendsServiceClient = $friendsServiceClient;
        $this->httpClient = $httpClient;
    }

    public function getDashboard(int $userId): array
    {
        try {
            Log::info("Fetching friend requests for user $userId");

            $responseData = $this->fetchFriendRequests($userId);

            $friendIds = array_column($responseData['friends'], 'friendId');
            $friendRequestIds = array_column($responseData['friendRequests'], 'requesterId');

            $users = $this->fetchUsersExcluding($friendIds, $friendRequestIds, $userId);

            $chats = $this->initializeChats($userId, $friendIds);

            $friends = $this->prepareFriends($chats, $userId);

            return $this->prepareDashboardData($friends, $responseData['friendRequests'], $users);
        } catch (DashboardException $e) {
            $this->logDashboardException($e, $userId);
        } catch (\Throwable $e) {
            $this->logException($e, $userId);
        }

        return $this->emptyDashboardData();
    }

    #[ArrayShape(['friendRequests' => "array", 'friends' => "array"])]
    private function fetchFriendRequests(int $userId): array
    {
        $request = new GetFriendRequestsByUserIdRequest();
        $request->setUserId($userId);

        list($response, $status) = $this->friendsServiceClient->GetFriendRequestsByUserId($request)->wait();

        if ($status->code !== \Grpc\STATUS_OK) {
            throw new DashboardException("Failed to fetch friend requests", $status->code);
        }

        return [
            'friendRequests' => $this->mapFriendRequests($response),
            'friends'        => $this->mapFriends($response),
        ];
    }

    private function fetchUsersExcluding(array $friendIds, array $friendRequestIds, int $userId): array
    {
        $mergedIds = array_merge($friendIds, $friendRequestIds, [$userId]);
        return User::whereNotIn('id', $mergedIds)->get()->toArray();
    }

    /**
     * @throws DashboardException
     */
    private function initializeChats(int $userId, array $friendIds): array
    {
        if (empty($friendIds)) {
            return [];
        }

        $userPairs = array_map(fn($friendId) => UserPairDTO::fromArray([
            'userId1' => $userId,
            'userId2' => $friendId
        ]), $friendIds);

        $response = $this->retryRequest(
            config('app.urls.chat_api') . "users/$userId/chats/initialize",
            ['userPairs' => $userPairs]
        );

        if ($response->getStatusCode() !== 200) {
            throw new DashboardException('Failed to fetch chats', $response->getStatusCode());
        }

        return json_decode($response->getBody()->getContents(), true)['data'];
    }

    private function retryRequest(string $url, array $data, int $retries = 3, int $sleep = 100): ResponseInterface
    {
        for ($attempt = 0; $attempt < $retries; $attempt++) {
            try {
                return $this->httpClient->post($url, [
                    'json' => $data
                ]);
            } catch (RequestException $e) {
                Log::error('Failed to fetch chats', [
                    'attempt' => $attempt + 1,
                    'error'   => $e->getMessage(),
                ]);

                usleep($sleep * 1000);
            }
        }

        throw new DashboardException('Failed to fetch chats after several retries.');
    }

    private function prepareFriends(array $chats, int $userId): array
    {
        return FriendTransformer::transformMany(
            array_map(function ($chat) use ($userId) {
                return [
                    'chatId'      => $chat['id'],
                    'friendId'    => $chat['user1'] != $userId ? $chat['user1'] : $chat['user2'],
                    'lastMessage' => $chat['lastMessage'] ?? null,
                ];
            }, $chats)
        );
    }

    private function prepareDashboardData(array $friends, array $friendRequests, array $users): array
    {
        return [
            'friendRequests' => FriendRequestTransformer::transformMany($friendRequests),
            'users'          => $users,
            'friends'        => $friends,
        ];
    }

    private function emptyDashboardData(): array
    {
        return [
            'friendRequests' => [],
            'users'          => [],
            'friends'        => [],
        ];
    }

    private function logDashboardException(DashboardException $e, int $userId): void
    {
        Log::error("DashboardException occurred", [
            'userId'  => $userId,
            'message' => $e->getMessage(),
            'code'    => $e->getCode(),
        ]);
    }

    private function logException(\Throwable $e, int $userId): void
    {
        Log::error("Exception occurred", [
            'userId'  => $userId,
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);
    }

    private function mapFriendRequests($response): array
    {
        return array_map(function ($friendRequest) {
            return [
                'id'          => $friendRequest->getId(),
                'requesterId' => $friendRequest->getRequesterId(),
            ];
        }, iterator_to_array($response->getFriendRequests()));
    }

    private function mapFriends($response): array
    {
        return array_map(function ($friend) {
            return [
                'friendId' => $friend->getFriendId(),
            ];
        }, iterator_to_array($response->getFriends()));
    }
}
