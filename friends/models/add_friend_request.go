package models

type AddFriendRequest struct {
	Data struct {
		RequesterId int `json:"requesterId"`
		RequestedId int `json:"requestedId"`
	} `json:"data"`
}
