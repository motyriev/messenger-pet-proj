import React, {useEffect, useRef, useState} from "react";
import Message from "./Message.jsx";
import MessageInput from "./MessageInput.jsx";
import DashboardBox from "./DashboardBox.jsx";
import { useAuth } from '../hooks/AuthProvider.jsx'

const ChatBox = () => {
    const [messages, setMessages] = useState([]);
    const [chatId, setChatId] = useState(() => {
        const savedChatId = localStorage.getItem('chatId');
        return savedChatId ? JSON.parse(savedChatId) : null;
    })
    const [currUser] = useState(useAuth().user);
    const scroll = useRef(null);

    const scrollToBottom = () => {
        if (scroll.current) {
            scroll.current.scrollIntoView({behavior: "smooth"});
        }
    }

    const connectWebSocket = () => {
        window.Echo.private('chat.' + chatId).listen('MessageSent', e => {
            console.log('ws Message: ' + JSON.stringify(e))
            setMessages(prevMessages => [...prevMessages, e.message]);
            setTimeout(scrollToBottom, 0);
        });
    }

    const getMessages = async () => {
        if (!chatId) {
            return;
        }

        try {
            const response = await axios.get(`api/chats/${chatId}/messages`);
            setMessages(response.data.data);
            setTimeout(scrollToBottom, 0);
        } catch (err) {
            console.log(err.message);
            if (err.response) {
                if(err.response.status === 401){
                    setChatId(null)
                    localStorage.setItem('chatId', '');
                }
            }
            console.log('getMessages error:', err.message);
        }
    };

    const handleChooseChat = async (chatId) => {
        try {
            setChatId(chatId)
            localStorage.setItem('chatId', chatId);
        } catch (err) {
            console.error(err.message);
        }
    };

    useEffect(() => {
        if (chatId) {
            getMessages();
            connectWebSocket();
        }

        return () => {
            if (chatId) {
                window.Echo.leave('chat.' + chatId);
            }
        }
    }, [chatId]);
    return (
        <div className="row justify-content-center">
            <DashboardBox callChooseChat={handleChooseChat} selectedChatId={chatId}/>
            {chatId && (<div className="col-md-6" style={{padding: 0}}>
                <div className="card" style={{
                    height: "600px",
                    overflowY: "auto",
                    borderTopLeftRadius: 0,
                    borderBottomLeftRadius: 0
                }}>
                    <div className="card-header">Chat Box</div>
                    <div className="card-body" style={{height: "500px", overflowY: "auto"}}>
                        {messages?.map((message) => (
                            <Message key={message.id}
                                     userId={currUser.id}
                                     message={message}
                            />
                        ))}
                        <span ref={scroll}></span>
                    </div>
                    <div className="card-footer">
                        <MessageInput userId={currUser.id} chatId={chatId}/>
                    </div>
                </div>
            </div>)}
        </div>
    );
};

export default ChatBox;
