import { useContext, createContext, useState } from "react";
import { useNavigate } from "react-router-dom";

const AuthContext = createContext();

const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(JSON.parse(localStorage.getItem("user")) || null);
    const [token, setToken] = useState(localStorage.getItem("token") || "");
    const navigate = useNavigate();
    const loginAction = async (data) => {
        try {
            const response = await axios.post(`/api/auth/login`, {
                email: data.email,
                password: data.password
            });

            if (response.data.accessToken) {
                window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.accessToken;
                window.Echo.connector.options.auth.headers.Authorization = 'Bearer ' + response.data.accessToken;
                window.Echo.disconnect();
                window.Echo.connect();
                setUser(response.data.user);
                setToken(response.data.accessToken);
                localStorage.setItem("token", response.data.accessToken);
                localStorage.setItem("user", JSON.stringify(response.data.user));
                navigate("/chat");
                return;
            }
            throw new Error(response.message);
        } catch (err) {
            console.error(err);
        }
    };

    const logOut = () => {
        setUser(null);
        setToken("");
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        window.axios.defaults.headers.common['Authorization'] = 'Bearer ';
        window.Echo.connector.options.auth.headers.Authorization = 'Bearer ' + newToken;
        window.Echo.disconnect();
        navigate("/login");
    };

    const updateToken = (newToken) => {
        setToken(newToken);
        localStorage.setItem("token", newToken);
        window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + newToken;
        window.Echo.connector.options.auth.headers.Authorization = 'Bearer ' + newToken;
        window.Echo.disconnect();
        window.Echo.connect();
        console.log('Token refreshed');
    };

    return (
        <AuthContext.Provider value={{ token, user, loginAction, logOut, updateToken }}>
            {children}
        </AuthContext.Provider>
    );

};

export default AuthProvider;

export const useAuth = () => {
    return useContext(AuthContext);
};
