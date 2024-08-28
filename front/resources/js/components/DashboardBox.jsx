import React, {useEffect, useRef, useState} from "react";
import DashboardInput from "./DashboardInput.jsx";
import User from "./User.jsx";
import {useAuth} from '../hooks/AuthProvider.jsx'
import axios from "axios";
import Friend from "./Friend";
import FriendRequest from "./FriendRequest.jsx";

const DashboardBox = ({callChooseChat, selectedChatId}) => {
    const {user: currUser, logOut} = useAuth();
    const [users, setUsers] = useState([]);
    const [friendRequests, setFriendRequests] = useState([]);
    const [friends, setFriends] = useState([]);
    const scroll = useRef();
    const {updateToken} = useAuth();

    const getDashboard = async () => {
        try {
            const r = await axios.get(`api/users/${currUser.id}/dashboard`);
            setUsers(r.data.users);
            setFriends(r.data.friends);
            setFriendRequests(r.data.friendRequests);
            console.log(r.data.friendRequests);
        } catch (err) {
            if (err.response && err.response.status === 401) {
                logOut();
            }
            console.log('get dashboard error:', err.message);
        }
    };

    const addToFriends = async (requestedUserId) => {
        try {
            const r = await axios.post(`api/users/${currUser.id}/friend-requests`, {
                requestedId: requestedUserId,
                requesterId: currUser.id
            });
            console.log(r);
        } catch (err) {
            console.log(err.message);
        }
    };

    const manageFriendsRequest = async (requestId, status) => {
        try {
            const r = await axios.patch(`api/users/${currUser.id}/friend-requests`, {
                requestId,
                status,
            });

            console.log(r);

            if (status === 'accepted') {
                setTimeout(async () => {
                    const refreshResponse = await axios.post('/api/auth/refresh');
                    const newToken = refreshResponse.data.accessToken;
                    updateToken(newToken);
                }, 2000);
                await getDashboard();
            }

        } catch (err) {
            console.log(err.message);
        }
    };

    const chooseChat = (chatId) => {
        callChooseChat(chatId);
    };

    useEffect(() => {
        getDashboard();
    }, []);

    return (
        <div className="col-md-2 p-0" style={{minWidth: "250px"}}>
            <div className="card h-100">
                <div className="card-header">
                    <DashboardInput/>
                </div>
                <div className="card-body p-2" style={{overflowY: "auto"}}>
                    <div className="list-group">
                        {friendRequests && (friendRequests.map((req) => (
                            <FriendRequest
                                key={req.id}
                                friendRequest={{id: req.id, email: req.email, requesterId: req.requesterId}}
                                manageFriendsRequest={manageFriendsRequest}
                            />
                        )))}
                        {friends && (friends.map((friend) => (
                            <Friend
                                key={friend.id}
                                friend={{
                                    id: friend.id,
                                    chatId: friend.chatId,
                                    email: friend.email,
                                    lastMessage: friend.lastMessage
                                }}
                                chooseChat={chooseChat}
                                isSelected={selectedChatId === friend.chatId}
                            />
                        )))}
                        {users && (users.map((user) => (
                            <User
                                key={user.id}
                                user={{id: user.id, email: user.email}}
                                addToFriends={addToFriends}
                            />
                        )))}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default DashboardBox;
