import React, { useState, useRef, useEffect } from 'react';
import { Card, Col, Row, Button, Form, InputGroup, Badge, Avatar } from 'react-bootstrap';
import { Head } from '@inertiajs/react';
import Layout from '../../../../Layouts';
import BreadCrumb from '../../../../Components/Common/BreadCrumb';
import FeatherIcon from 'feather-icons-react';
import SimpleBar from 'simplebar-react';
import EmojiPicker from 'emoji-picker-react';
import { useTranslation } from 'react-i18next';

const TicketChat = () => {
    const { t } = useTranslation();
    const [messages, setMessages] = useState([
        {
            id: 1,
            sender: 'Support Team',
            message: 'Hello! Thank you for contacting support. How can we help you today?',
            timestamp: '10:30 AM',
            isSupport: true,
            avatar: '/images/users/avatar-1.jpg'
        },
        {
            id: 2,
            sender: 'You',
            message: 'Hi, I\'m having issues with my power meter readings. The data seems inconsistent.',
            timestamp: '10:32 AM',
            isSupport: false,
            avatar: '/images/users/avatar-2.jpg'
        },
        {
            id: 3,
            sender: 'Support Team',
            message: 'I understand. Can you please provide the device ID and describe the specific inconsistencies you\'re seeing?',
            timestamp: '10:33 AM',
            isSupport: true,
            avatar: '/images/users/avatar-1.jpg'
        },
        {
            id: 4,
            sender: 'You',
            message: 'Device ID: PM-001. The readings show sudden spikes and drops that don\'t match our actual usage.',
            timestamp: '10:35 AM',
            isSupport: false,
            avatar: '/images/users/avatar-2.jpg'
        }
    ]);

    const [newMessage, setNewMessage] = useState('');
    const [showEmojiPicker, setShowEmojiPicker] = useState(false);
    const chatRef = useRef<any>(null);

    const sendMessage = () => {
        if (newMessage.trim()) {
            const message = {
                id: messages.length + 1,
                sender: 'You',
                message: newMessage,
                timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                isSupport: false,
                avatar: '/images/users/avatar-2.jpg'
            };
            setMessages([...messages, message]);
            setNewMessage('');
            setShowEmojiPicker(false);
        }
    };

    const onEmojiClick = (emojiObject: any) => {
        setNewMessage(prev => prev + emojiObject.emoji);
    };

    useEffect(() => {
        if (chatRef.current) {
            chatRef.current.scrollTop = chatRef.current.scrollHeight;
        }
    }, [messages]);

    return (
        <React.Fragment>
            <Head title={t('Ticket Chat') + ' | Power Dashboard'} />
            <div className="page-content">
                <div className="container-fluid">
                    <BreadCrumb title={'Ticket #VLZ135 - ' + t('Chat')} pageTitle={t('Support')} />
                    
                    <Row>
                        <Col lg={8}>
                            <Card>
                                <Card.Header>
                                    <div className="d-flex align-items-center">
                                        <div className="flex-grow-1">
                                            <h5 className="card-title mb-0">Ticket #VLZ135 - Power Meter Issues</h5>
                                            <p className="text-muted mb-0">{t('Status')}: <Badge bg="warning">In Progress</Badge></p>
                                        </div>
                                        <div className="flex-shrink-0">
                                            <Button variant="outline-primary" size="sm" className="me-2">
                                                <FeatherIcon icon="download" className="icon-sm me-1" />
                                                {t('Export Chat')}
                                            </Button>
                                            <Button variant="success" size="sm">
                                                <FeatherIcon icon="check" className="icon-sm me-1" />
                                                {t('Resolve Ticket')}
                                            </Button>
                                        </div>
                                    </div>
                                </Card.Header>
                                <Card.Body className="p-0">
                                    <div className="chat-conversation p-3 p-lg-4" id="chat-conversation">
                                        <SimpleBar ref={chatRef} style={{ height: "400px" }}>
                                            <ul className="list-unstyled chat-conversation-list" id="users-conversation">
                                                {messages.map((msg) => (
                                                    <li key={msg.id} className={msg.isSupport ? "chat-list left" : "chat-list right"}>
                                                        <div className="conversation-list">
                                                            {msg.isSupport && (
                                                                <div className="chat-avatar">
                                                                    <img src={msg.avatar} alt="" className="rounded-circle avatar-xs" />
                                                                </div>
                                                            )}
                                                            <div className="user-chat-content">
                                                                <div className="ctext-wrap">
                                                                    <div className="ctext-wrap-content">
                                                                        <p className="mb-0 ctext-content">{msg.message}</p>
                                                                        <p className="chat-time mb-0">
                                                                            <i className="ri-time-line align-bottom"></i> {msg.timestamp}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {!msg.isSupport && (
                                                                <div className="chat-avatar">
                                                                    <img src={msg.avatar} alt="" className="rounded-circle avatar-xs" />
                                                                </div>
                                                            )}
                                                        </div>
                                                    </li>
                                                ))}
                                            </ul>
                                        </SimpleBar>
                                    </div>
                                    
                                    <div className="chat-input-section p-3 p-lg-4 border-top user-chat">
                                        <div className="row g-2 align-items-center">
                                            <div className="col">
                                                <div className="position-relative">
                                                    <InputGroup>
                                                        <Form.Control
                                                            type="text"
                                                            className="chat-input"
                                                            placeholder={t('Type your message')}
                                                            value={newMessage}
                                                            onChange={(e) => setNewMessage(e.target.value)}
                                                            onKeyPress={(e) => e.key === 'Enter' && sendMessage()}
                                                        />
                                                        <Button
                                                            variant="light"
                                                            onClick={() => setShowEmojiPicker(!showEmojiPicker)}
                                                        >
                                                            <FeatherIcon icon="smile" className="icon-sm" />
                                                        </Button>
                                                    </InputGroup>
                                                    {showEmojiPicker && (
                                                        <div className="position-absolute bottom-100 end-0 mb-2">
                                                            <EmojiPicker onEmojiClick={onEmojiClick} />
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                            <div className="col-auto">
                                                <Button variant="primary" onClick={sendMessage}>
                                                    <FeatherIcon icon="send" className="icon-sm" />
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </Card.Body>
                            </Card>
                        </Col>
                        
                        <Col lg={4}>
                            <Card>
                                <Card.Header>
                                    <h5 className="card-title mb-0">{t('Ticket Information')}</h5>
                                </Card.Header>
                                <Card.Body>
                                    <div className="table-responsive">
                                        <table className="table table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <td className="fw-medium">{t('Ticket ID')}:</td>
                                                    <td>#VLZ135</td>
                                                </tr>
                                                <tr>
                                                    <td className="fw-medium">{t('Subject')}:</td>
                                                    <td>Power Meter Reading Issues</td>
                                                </tr>
                                                <tr>
                                                    <td className="fw-medium">{t('Priority')}:</td>
                                                    <td><Badge bg="warning">Medium</Badge></td>
                                                </tr>
                                                <tr>
                                                    <td className="fw-medium">{t('Status')}:</td>
                                                    <td><Badge bg="info">In Progress</Badge></td>
                                                </tr>
                                                <tr>
                                                    <td className="fw-medium">{t('Created')}:</td>
                                                    <td>Jan 15, 2024</td>
                                                </tr>
                                                <tr>
                                                    <td className="fw-medium">{t('Assigned To')}:</td>
                                                    <td>Support Team</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </Card.Body>
                            </Card>
                            
                            <Card>
                                <Card.Header>
                                    <h5 className="card-title mb-0">{t('Quick Actions')}</h5>
                                </Card.Header>
                                <Card.Body>
                                    <div className="d-grid gap-2">
                                        <Button variant="outline-primary" size="sm">
                                            <FeatherIcon icon="file-text" className="icon-sm me-2" />
                                            {t('Add Note')}
                                        </Button>
                                        <Button variant="outline-info" size="sm">
                                            <FeatherIcon icon="paperclip" className="icon-sm me-2" />
                                            {t('Attach File')}
                                        </Button>
                                        <Button variant="outline-warning" size="sm">
                                            <FeatherIcon icon="clock" className="icon-sm me-2" />
                                            {t('Escalate')}
                                        </Button>
                                        <Button variant="outline-success" size="sm">
                                            <FeatherIcon icon="check-circle" className="icon-sm me-2" />
                                            {t('Mark Resolved')}
                                        </Button>
                                    </div>
                                </Card.Body>
                            </Card>
                        </Col>
                    </Row>
                </div>
            </div>
        </React.Fragment>
    );
};

TicketChat.layout = (page: any) => <Layout children={page} />;
export default TicketChat;
