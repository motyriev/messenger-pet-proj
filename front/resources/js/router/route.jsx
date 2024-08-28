import React from "react";
import { Navigate, Outlet } from "react-router-dom";
import { useAuth } from "../hooks/AuthProvider";

const PrivateRoute = () => {
    if (!useAuth().token || !useAuth().user) return <Navigate to="/login" />;
    return <Outlet />;
};


const PublicRoute = () => {
    if (useAuth().token && useAuth().user) return <Navigate to="/chat" />;
    return <Outlet />;
};

export {PrivateRoute, PublicRoute};
