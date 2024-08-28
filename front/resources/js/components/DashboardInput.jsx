
import React, { useState } from "react";

const DashboardInput = () => {
    const [message, setMessage] = useState("");

    return (
        <div className="input-group" >
            <input onChange={(e) => setMessage(e.target.value)}
                   autoComplete="off"
                   type="text"
                   className="form-control"
                   placeholder="Search..."
                   value={message}
            />
        </div>
    );
};

export default DashboardInput;
