import React from "react";
import { Link } from "react-router-dom";

const NotFound = () => {
    return (
        <div className="container d-flex flex-column align-items-center justify-content-center vh-60">
            <div className="text-center">
                <h1 className="display-1 fw-bold text-primary">404</h1>
                <p className="fs-3">
                    <span className="text-danger">Oops!</span> Page not found.
                </p>
                <p className="lead">
                    The page you’re looking for doesn’t exist.
                </p>
                <Link to="/chat" className="btn btn-primary">
                    Go Back Home
                </Link>
            </div>
        </div>
    );
};

export default NotFound;
