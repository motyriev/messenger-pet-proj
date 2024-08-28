
import React, { useState } from "react";
import {useAuth} from "../hooks/AuthProvider.jsx";

const MessageInput = ({ userId, chatId }) => {
    const [message, setMessage] = useState("");

    const messageRequest = async (text) => {
        try {
           const response = await axios.post(`api/chats/${chatId}/message`, {
               body: text,
                chatId: chatId,
                userId: userId
            });
            console.log(response);
        } catch (err) {
            console.log(err.message);
        }
    };

    const sendMessage = (e) => {
        e.preventDefault();
        if (message.trim() === "") {
            alert("Please enter a message!");
            return;
        }

        messageRequest(message);
        setMessage("");
    };

    return (
        <div className="input-group">
            <input onChange={(e) => setMessage(e.target.value)}
                   autoComplete="off"
                   type="text"
                   className="form-control"
                   placeholder="Message..."
                   value={message}
            />
            <div className="input-group-append">
                <button onClick={(e) => sendMessage(e)}
                        className="btn btn-primary"
                        type="button">Send</button>
            </div>
        </div>
    );
};

export default MessageInput;
//
