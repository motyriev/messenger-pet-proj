<?php
// GENERATED CODE -- DO NOT EDIT!

namespace FriendsService;

/**
 */
class FriendsServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \FriendsService\GetFriendRequestsByUserIdRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetFriendRequestsByUserId(\FriendsService\GetFriendRequestsByUserIdRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/FriendsService.FriendsService/GetFriendRequestsByUserId',
        $argument,
        ['\FriendsService\GetFriendRequestsByUserIdResponse', 'decode'],
        $metadata, $options);
    }

}
