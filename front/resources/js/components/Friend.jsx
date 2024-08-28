import React from "react";

const Friend = ({friend, chooseChat, isSelected}) => {
    const handleClick = () => {
        chooseChat(friend.chatId);
    };

    const truncateMessage = (message) => {
        return message.length > 27 ? message.slice(0, 27) + "..." : message;
    };

    return (
        <div
            className={`list-group-item list-group-item-action ${isSelected ? "active" : ""}`}
            onClick={handleClick}
            style={{cursor: "pointer"}}
        >
            <div>
                <span>{friend.email}</span>
            </div>
            <small className="text-muted d-block mt-1">
                {truncateMessage(friend.lastMessage)}
            </small>
        </div>
    );
};

export default Friend;
