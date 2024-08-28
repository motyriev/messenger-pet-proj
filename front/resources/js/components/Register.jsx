import {useState} from "react";
import {useNavigate} from "react-router-dom";

const Login = () => {
    const navigate = useNavigate();
    const [input, setInput] = useState({
        email: "",
        password: "",
    });

    const handleSubmitEvent = async (e) => {
        e.preventDefault();
        await axios.post(`/api/auth/register`, {
            email: input.email,
            password: input.password
        });

        navigate('/login');
    };

    const handleInput = (e) => {
        const {name, value} = e.target;
        setInput((prev) => ({
            ...prev,
            [name]: value,
        }));
    };

    return (
        <div className="row justify-content-center">
            <div className="col-md-8">
                <div className="card">
                    <div className="card-header">Register</div>
                    <div className="card-body" style={{height: "400px", overflowY: "auto", textAlign: "center"}}>
                        <form style={{display: "inline-block"}} onSubmit={handleSubmitEvent}>
                            <div className="mb-3">
                                <label htmlFor="user-email">Email:</label>
                                <input
                                    type="email"
                                    id="user-email"
                                    name="email"
                                    className="form-control"
                                    placeholder="example@yahoo.com"
                                    aria-describedby="user-email"
                                    aria-invalid="false"
                                    onChange={handleInput}
                                />
                            </div>
                            <div className="mb-3">
                                <label htmlFor="password">Password:</label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    className="form-control"
                                    aria-describedby="user-password"
                                    aria-invalid="false"
                                    onChange={handleInput}
                                />
                            </div>
                            <button className="btn btn-primary">Submit</button>
                            <div className="mb-3">
                                <a href="/login">login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Login;
