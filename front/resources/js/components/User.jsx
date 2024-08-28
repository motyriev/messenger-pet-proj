import React from "react";

const User = ({ user, addToFriends }) => {
    const handleClick = () => {
        addToFriends(user.id);
    };

    return (
        <div className="list-group-item d-flex justify-content-between align-items-center">
            <span>{user.email}</span>
            <button className="btn btn-sm btn-outline-primary" onClick={handleClick}>
                Add
            </button>
        </div>
    );
};

export default User;
