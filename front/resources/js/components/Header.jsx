import {useAuth} from '../hooks/AuthProvider.jsx'

const Header = () => {
    if (useAuth().user) {
        return (
            <div className="row justify-content-center" style={{}}>
                <div className="col-md-6" style={{padding: 0, width: "910px", paddingBottom: '30px'}}>
                    <div className="card" style={{
                        height: "50px",
                        borderTopLeftRadius: 0,
                        borderTopRightRadius: 0,
                        backgroundColor: "rgba(var(--bs-body-color-rgb), 0.03)"
                    }}>
                        <div className="card-body" style={{height: "500px", overflow: "hidden"}}>
                            <span>Hello, <span style={{fontStyle: 'italic'}}>{useAuth().user.name}</span></span>
                            <a className="btn float-end" href="#" onClick={useAuth().logOut}>Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
};

export default Header;
