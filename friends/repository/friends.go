package repository

import (
    "friends/db"
    "friends/models"
)

func GetFriendsByUserId(userId int) ([]models.Friend, error) {
    query := "SELECT user_2 AS friend_id FROM friends WHERE user_1 = $1 UNION SELECT user_1 AS friend_id FROM friends WHERE user_2 = $1"
    rows, err := db.Query(query, userId)
    if err != nil {
        return nil, err
    }
    defer rows.Close()

    var friends []models.Friend
    for rows.Next() {
        var request models.Friend
        err := rows.Scan(&request.FriendId)
        if err != nil {
            return nil, err
        }
        friends = append(friends, request)
    }
    if err = rows.Err(); err != nil {
        return nil, err
    }

    return friends, nil
}
