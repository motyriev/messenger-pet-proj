package main

import (
	"encoding/json"
	"friends/amqp"
	"friends/db"
	"friends/models"
	"friends/repository"
	"friends/server"
	"log"
	"os"
	"os/signal"
)

func main() {
	db.InitDB()
	defer db.CloseDB()
	db.InitSchema()

	if len(os.Args) > 1 && os.Args[1] == "seed" {
		db.SeedDB("db/seed_data.sql")
		return
	}

	go func() {
		server.Run() //gRPC server
	}()

	stopChan := make(chan os.Signal, 1)
	signal.Notify(stopChan, os.Interrupt)

	connection, channel, msgs := amqp.Init()
	defer connection.Close()
	defer channel.Close()

	go func() {
		for msg := range msgs["add_friend_request_queue"] {
			log.Printf("Received message from the add_friend_request_queue: %s", msg.Body)
			handleAddFriendRequest(msg.Body)
		}
	}()

	go func() {
		for msg := range msgs["manage_friend_request_queue"] {
			log.Printf("Received message from the manage_friend_request_queue: %s", msg.Body)
			handleManageFriendRequest(msg.Body)
		}
	}()

	log.Println("Waiting for messages. Press Ctrl+C to exit.")
	<-stopChan
	log.Println("Exiting")
}

func handleAddFriendRequest(msgBody []byte) {
	var request models.AddFriendRequest
	err := json.Unmarshal(msgBody, &request)
	if err != nil {
		log.Printf("Error parsing JSON for AddFriendRequest: %v, message: %s", err, msgBody)
		return
	}

	log.Printf("Handling AddFriendRequest: RequesterId=%d, RequestedId=%d", request.Data.RequesterId, request.Data.RequestedId)

	err = repository.StoreFriendRequest(request.Data.RequesterId, request.Data.RequestedId)
	if err != nil {
		log.Printf("Failed to store friend request: RequesterId=%d, RequestedId=%d, Error=%v", request.Data.RequesterId, request.Data.RequestedId, err)
		return
	}

	log.Printf("Successfully stored friend request: RequesterId=%d, RequestedId=%d", request.Data.RequesterId, request.Data.RequestedId)
}

func handleManageFriendRequest(msgBody []byte) {
	var request models.ManageFriendRequest
	err := json.Unmarshal(msgBody, &request)
	if err != nil {
		log.Printf("Error parsing JSON for ManageFriendRequest: %v, message: %s", err, msgBody)
		return
	}

	log.Printf("Handling ManageFriendRequest: RequestId=%d, Status=%d", request.Data.RequestId, request.Data.Status)

	err = repository.ManageFriendRequest(request.Data.RequestId, request.Data.Status)
	if err != nil {
		log.Printf("Failed to manage friend request: RequestId=%d, Status=%d, Error=%v", request.Data.RequestId, request.Data.Status, err)
		return
	}

	log.Printf("Successfully managed friend request: RequestId=%d, Status=%d", request.Data.RequestId, request.Data.Status)
}
