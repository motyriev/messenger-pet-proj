package repository

import (
    "friends/db"
    "friends/models"
	"fmt"
)

func GetFriendRequestsByUserId(userId int) ([]models.FriendRequest, error) {
    query := "SELECT id, requester_id FROM friend_requests WHERE requested_id = $1 AND status_id = $2"
    rows, err := db.Query(query, userId, 1)
    if err != nil {
        return nil, err
    }
    defer rows.Close()

    var friendRequests []models.FriendRequest
    for rows.Next() {
        var request models.FriendRequest
        err := rows.Scan(&request.Id, &request.RequesterId)
        if err != nil {
            return nil, err
        }
        friendRequests = append(friendRequests, request)
    }
    if err = rows.Err(); err != nil {
        return nil, err
    }

    return friendRequests, nil
}

func StoreFriendRequest(requesterId int, requestedId int) error {
    var exists bool
    err := db.QueryRow("SELECT EXISTS(SELECT 1 FROM friend_requests WHERE requester_id = $1 AND requested_id = $2)", requesterId, requestedId).Scan(&exists)
    if err != nil {
        return err
    }

    if !exists {
        _, err = db.Exec("INSERT INTO friend_requests (requester_id, requested_id, status_id) VALUES ($1, $2, $3)", requesterId, requestedId, 1)
        if err != nil {
            return err
        }
    }

    return nil
}

func ManageFriendRequest(requestId int, status string) error {
    var exists bool
    err := db.QueryRow("SELECT EXISTS(SELECT 1 FROM friend_requests WHERE id = $1)", requestId).Scan(&exists)
    if err != nil {
        return err
    }

    if !exists {
        panic(fmt.Sprintf("friend_request %v does not exists: ", requestId))
    }

    statusId := 1

    switch status {
    case "accepted":
        statusId = 2
        tx, err := db.TxBegin()
        if err != nil {
            return err
        }

        var requestedId int
        var requesterId int
        err = db.QueryRow("UPDATE friend_requests SET status_id = $1 WHERE id = $2 RETURNING requested_id, requester_id", statusId, requestId).Scan(&requestedId, &requesterId)
        if err != nil {
            tx.Rollback()
            return err
        }

        _, err = tx.Exec("INSERT INTO friends (user_1, user_2) VALUES ($1, $2)", requesterId, requestedId)
        if err != nil {
            tx.Rollback()
            return err
        }

        err = tx.Commit()
        if err != nil {
            return err
        }

    case "declined":
        statusId = 3
        _, err = db.Exec("UPDATE friend_requests SET status_id = $1 WHERE id = $2", statusId, requestId)
        if err != nil {
            return err
        }
    default:
        panic(fmt.Sprintf("status %v does not exists: ", status))
    }

    return nil
}
