package server

import (
	"context"
	proto "friends/proto"
	"friends/repository"
	"log"
	"net"
	"google.golang.org/grpc"
)

type FriendsServer struct {
	proto.UnimplementedFriendsServiceServer
}

func Run() {
	log.Println("Starting gRPC server setup...")

	lis, err := net.Listen("tcp", ":50051")
	if err != nil {
		log.Fatalf("Failed to listen: %v", err)
	}
	log.Println("Listening on :50051")

	grpcServer := grpc.NewServer()
	proto.RegisterFriendsServiceServer(grpcServer, &FriendsServer{})
	log.Println("FriendsServiceServer registered")

	log.Println("Starting gRPC server...")
	if err := grpcServer.Serve(lis); err != nil {
		log.Fatalf("Failed to serve: %v", err)
	}
}

func (s *FriendsServer) GetFriendRequestsByUserId(ctx context.Context, req *proto.GetFriendRequestsByUserIdRequest) (*proto.GetFriendRequestsByUserIdResponse, error) {
	log.Printf("Received GetFriendRequestsByUserId request: %+v", req)

	friendRequests, err := repository.GetFriendRequestsByUserId(int(req.UserId))
	if err != nil {
		log.Printf("Error fetching friend requests: %v", err)
		return nil, err
	}
	log.Printf("Fetched Friend Requests: %+v", friendRequests)

	friends, err := repository.GetFriendsByUserId(int(req.UserId))
	if err != nil {
		log.Printf("Error fetching friends: %v", err)
		return nil, err
	}
	log.Printf("Fetched Friends: %+v", friends)

	response := &proto.GetFriendRequestsByUserIdResponse{
		FriendRequests: []*proto.FriendRequest{},
		Friends:        []*proto.Friend{},
	}
	log.Println("Initialized response structure")

	for _, fr := range friendRequests {
		log.Printf("Processing FriendRequest: %+v", fr)
		response.FriendRequests = append(response.FriendRequests, &proto.FriendRequest{
			Id:          int32(fr.Id),
			RequesterId: int32(fr.RequesterId),
		})
	}
	log.Println("Populated FriendRequests in response")

	for _, f := range friends {
		log.Printf("Processing Friend: %+v", f)
		response.Friends = append(response.Friends, &proto.Friend{
			FriendId: int32(f.FriendId),
		})
	}
	log.Println("Populated Friends in response")

	log.Printf("Final Response: FriendRequests=%+v, Friends=%+v", response.FriendRequests, response.Friends)
	return response, nil
}
