import React from "react";

const Message = ({userId, message}) => {
    const createdAt = new Date(message.createdAt);
    const options = { month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    const formattedDate = createdAt.toLocaleString('en-GB', options);
    return (
        <div className={`row ${userId === message.userId ? "justify-content-end" : ""}`}>
            <div className="col-md-6">
                <div className="d-flex">
                    <small className="text-muted">{message.userEmail}</small><br/>
                    <small className="text-muted ms-auto">
                        {formattedDate}
                    </small></div>
                <div className={`alert alert-${userId === message.userId ? "primary" : "secondary"}`} role="alert">
                    {message.body}
                </div>

            </div>
        </div>
    );
};

export default Message;
