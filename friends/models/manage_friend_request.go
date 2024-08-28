package models

type ManageFriendRequest struct {
	Data struct {
		RequestId int    `json:"requestId"`
		Status    string `json:"status"`
	} `json:"data"`
}
