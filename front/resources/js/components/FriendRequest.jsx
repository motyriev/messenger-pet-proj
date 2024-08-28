import React from "react";

const FriendRequest = ({friendRequest, manageFriendsRequest}) => {
    const handleClick = (status) => {
        manageFriendsRequest(friendRequest.id, status);
    };

    return (
        <div className="list-group-item d-flex flex-column align-items-center" style={styles.container}>
            <span style={styles.email}>{friendRequest.email}</span>
            <div style={styles.buttonContainer}>
                <button className="btn btn-sm btn-outline-success" style={styles.button}
                        onClick={() => handleClick('accepted')}>Accept
                </button>
                <button className="btn btn-sm btn-outline-danger" style={styles.button}
                        onClick={() => handleClick('declined')}>Decline
                </button>
            </div>
        </div>
    );
};

const styles = {
    container: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        width: '100%',
        border: '1px solid rgba(0,0,0,0.1)',
    },
    email: {
        maxWidth: '90%',
        overflow: 'hidden',
        textOverflow: 'ellipsis',
        whiteSpace: 'nowrap'
    },
    buttonContainer: {
        display: 'flex',
        justifyContent: 'center',
        width: '100%'
    },
    button: {
        margin: '0 5px',
    }
};

export default FriendRequest;
