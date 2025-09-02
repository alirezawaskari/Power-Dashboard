import React, { useState } from 'react';
import { Card, CardBody, CardHeader, Row, Col, Button, Badge, Form, InputGroup, ListGroup } from 'react-bootstrap';
import { Head } from '@inertiajs/react';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import Layout from "../../../Layouts";
import { useTranslation } from 'react-i18next';

const Documents = () => {
    const { t } = useTranslation();
    const [documents] = useState([
        {
            id: 1,
            name: t('Power Dashboard User Manual'),
            type: 'PDF',
            size: '2.5 MB',
            category: t('User Guides'),
            uploaded: '2024-01-15',
            downloads: 45,
            description: t('Complete user manual for the power dashboard system')
        },
        {
            id: 2,
            name: t('Device Installation Guide'),
            type: 'PDF',
            size: '1.8 MB',
            category: t('Installation'),
            uploaded: '2024-01-14',
            downloads: 32,
            description: t('Step-by-step guide for installing power monitoring devices')
        },
        {
            id: 3,
            name: t('API Documentation'),
            type: 'PDF',
            size: '3.2 MB',
            category: t('Technical'),
            uploaded: '2024-01-13',
            downloads: 28,
            description: t('Complete API reference for developers')
        },
        {
            id: 4,
            name: t('Troubleshooting Guide'),
            type: 'PDF',
            size: '1.5 MB',
            category: t('Support'),
            uploaded: '2024-01-12',
            downloads: 67,
            description: t('Common issues and their solutions')
        },
        {
            id: 5,
            name: t('System Architecture Overview'),
            type: 'PDF',
            size: '4.1 MB',
            category: t('Technical'),
            uploaded: '2024-01-11',
            downloads: 19,
            description: t('Detailed system architecture documentation')
        },
        {
            id: 6,
            name: t('Alert Configuration Guide'),
            type: 'PDF',
            size: '1.2 MB',
            category: t('Configuration'),
            uploaded: '2024-01-10',
            downloads: 38,
            description: t('How to configure alerts and notifications')
        }
    ]);

    const [selectedCategory, setSelectedCategory] = useState('all');
    const [searchTerm, setSearchTerm] = useState('');

    const categories = ['all', t('User Guides'), t('Installation'), t('Technical'), t('Support'), t('Configuration')];

    const filteredDocuments = documents.filter(doc => {
        if (selectedCategory !== 'all' && doc.category !== selectedCategory) return false;
        if (searchTerm && !doc.name.toLowerCase().includes(searchTerm.toLowerCase())) return false;
        return true;
    });

    const getFileIcon = (type: string) => {
        switch (type) {
            case 'PDF': return 'ri-file-pdf-line text-danger';
            case 'DOC': return 'ri-file-word-line text-primary';
            case 'XLS': return 'ri-file-excel-line text-success';
            default: return 'ri-file-line text-secondary';
        }
    };

    return (
        <React.Fragment>
            <Head title={t('Documents') + ' | ' + t('power_dashboard.pages.dashboard.title')} />
            <div className="page-content">
                <div className="container-fluid">
                    <BreadCrumb title={t('Documents')} pageTitle={t('Dashboard')} />
                    
                    <Row>
                        <Col lg={12}>
                            <Card>
                                <CardHeader>
                                    <div className="d-flex align-items-center justify-content-between">
                                        <h4 className="card-title mb-0">{t('Document Library')}</h4>
                                        <Button variant="primary" size="sm">
                                            <i className="ri-upload-line me-1"></i>
                                            {t('Upload Document')}
                                        </Button>
                                    </div>
                                </CardHeader>
                                <CardBody>
                                    <Row className="mb-3">
                                        <Col md={6}>
                                            <InputGroup>
                                                <InputGroup.Text>
                                                    <i className="ri-search-line"></i>
                                                </InputGroup.Text>
                                                <Form.Control
                                                    type="text"
                                                    placeholder={t('Search documents...')}
                                                    value={searchTerm}
                                                    onChange={(e) => setSearchTerm(e.target.value)}
                                                />
                                            </InputGroup>
                                        </Col>
                                        <Col md={6}>
                                            <Form.Select
                                                value={selectedCategory}
                                                onChange={(e) => setSelectedCategory(e.target.value)}
                                            >
                                                {categories.map(category => (
                                                    <option key={category} value={category}>
                                                        {category === 'all' ? t('All Categories') : category}
                                                    </option>
                                                ))}
                                            </Form.Select>
                                        </Col>
                                    </Row>

                                    <Row>
                                        {filteredDocuments.map((doc) => (
                                            <Col lg={4} md={6} key={doc.id}>
                                                <Card className="mb-3">
                                                    <CardBody>
                                                        <div className="d-flex align-items-start">
                                                            <div className="flex-shrink-0">
                                                                <div className="avatar-sm">
                                                                    <span className="avatar-title bg-light rounded">
                                                                        <i className={`${getFileIcon(doc.type)} fs-20`}></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div className="flex-grow-1 ms-3">
                                                                <h6 className="mb-1">{doc.name}</h6>
                                                                <p className="text-muted mb-2 small">{doc.description}</p>
                                                                <div className="d-flex align-items-center justify-content-between">
                                                                    <div>
                                                                        <Badge bg="light" text="dark" className="me-2">
                                                                            {doc.category}
                                                                        </Badge>
                                                                        <small className="text-muted">
                                                                            {doc.size} • {doc.downloads} {t('downloads')}
                                                                        </small>
                                                                    </div>
                                                                    <Button variant="outline-primary" size="sm">
                                                                        <i className="ri-download-line me-1"></i>
                                                                        {t('Download')}
                                                                    </Button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </CardBody>
                                                </Card>
                                            </Col>
                                        ))}
                                    </Row>
                                </CardBody>
                            </Card>
                        </Col>
                    </Row>

                    <Row>
                        <Col lg={4}>
                            <Card>
                                <CardHeader>
                                    <h5 className="card-title mb-0">{t('Document Categories')}</h5>
                                </CardHeader>
                                <CardBody>
                                    <ListGroup variant="flush">
                                        {categories.filter(cat => cat !== 'all').map((category) => (
                                            <ListGroup.Item key={category} action>
                                                <div className="d-flex justify-content-between align-items-center">
                                                    <span>{category}</span>
                                                    <Badge bg="primary" pill>
                                                        {documents.filter(doc => doc.category === category).length}
                                                    </Badge>
                                                </div>
                                            </ListGroup.Item>
                                        ))}
                                    </ListGroup>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={8}>
                            <Card>
                                <CardHeader>
                                    <h5 className="card-title mb-0">{t('Recent Uploads')}</h5>
                                </CardHeader>
                                <CardBody>
                                    <div className="table-responsive">
                                        <table className="table table-nowrap align-middle">
                                            <thead className="table-light">
                                                <tr>
                                                    <th>{t('Document')}</th>
                                                    <th>{t('Category')}</th>
                                                    <th>{t('Size')}</th>
                                                    <th>{t('Uploaded')}</th>
                                                    <th>{t('Downloads')}</th>
                                                    <th>{t('Action')}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {documents.slice(0, 5).map((doc) => (
                                                    <tr key={doc.id}>
                                                        <td>
                                                            <div className="d-flex align-items-center">
                                                                <i className={`${getFileIcon(doc.type)} fs-18 me-2`}></i>
                                                                <span className="fw-medium">{doc.name}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <Badge bg="light" text="dark">{doc.category}</Badge>
                                                        </td>
                                                        <td>{doc.size}</td>
                                                        <td>{doc.uploaded}</td>
                                                        <td>{doc.downloads}</td>
                                                        <td>
                                                            <Button variant="outline-primary" size="sm">
                                                                <i className="ri-download-line"></i>
                                                            </Button>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </CardBody>
                            </Card>
                        </Col>
                    </Row>
                </div>
            </div>
        </React.Fragment>
    );
};

Documents.layout = (page: any) => <Layout children={page} />
export default Documents;
