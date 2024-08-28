import React from 'react';
import ReactDOM from 'react-dom/client';
import '../../css/app.css';
import ChatBox from "./ChatBox.jsx";
import Login from "./Login";
import AuthProvider from "../hooks/AuthProvider";
import {PrivateRoute, PublicRoute} from "../router/route"
import {BrowserRouter as Router, Route, Routes} from "react-router-dom";
import Register from "./Register";
import Header from "./Header.jsx";
import NotFound from "./NotFound.jsx";

if (document.getElementById('main')) {
    ReactDOM.createRoot(document.getElementById('main')).render(
        <React.StrictMode>
            <Router>
                <AuthProvider>
                    <Header/>
                    <Routes>
                        <Route element={<PublicRoute/>}>
                            <Route path="/login" element={<Login/>}/>
                            <Route path="/register" element={<Register/>}/>
                        </Route>
                        <Route element={<PrivateRoute/>}>
                            <Route path="/chat" element={<ChatBox/>}/>
                        </Route>
                        <Route path="*" element={<NotFound />} />
                    </Routes>
                </AuthProvider>
            </Router>
        </React.StrictMode>
    );
}
