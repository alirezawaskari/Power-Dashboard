import React, { useState } from 'react';
import { Card, Col, Container, Row, Form, Button, Alert, Badge } from 'react-bootstrap';
import { Head } from '@inertiajs/react';
import Layout from '../../../Layouts';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import FeatherIcon from 'feather-icons-react';
import { useTranslation } from 'react-i18next';

const SystemSettings = () => {
    const { t } = useTranslation();
    const [settings, setSettings] = useState({
        systemName: 'Power Dashboard System',
        timezone: 'UTC',
        dataRetention: '90',
        alertThreshold: '85',
        autoBackup: true,
        emailNotifications: true,
        smsNotifications: false,
        maintenanceMode: false
    });

    const [showAlert, setShowAlert] = useState(false);

    const handleSave = () => {
        setShowAlert(true);
        setTimeout(() => setShowAlert(false), 3000);
    };

    return (
        <React.Fragment>
            <Head title={t('System Settings') + ' | Power Dashboard'} />
            <div className="page-content">
                <Container fluid>
                    <BreadCrumb title={t('System Settings')} pageTitle={t('Configuration')} />
                    
                    {showAlert && (
                        <Alert variant="success" onClose={() => setShowAlert(false)} dismissible>
                            <FeatherIcon icon="check-circle" className="icon-sm me-2" />
                            {t('Settings saved successfully!')}
                        </Alert>
                    )}

                    <Row>
                        <Col lg={8}>
                            <Card>
                                <Card.Header>
                                    <h4 className="card-title mb-0">{t('General Settings')}</h4>
                                </Card.Header>
                                <Card.Body>
                                    <Form>
                                        <Row>
                                            <Col md={6}>
                                                <Form.Group className="mb-3">
                                                    <Form.Label>{t('System Name')}</Form.Label>
                                                    <Form.Control
                                                        type="text"
                                                        value={settings.systemName}
                                                        onChange={(e) => setSettings({...settings, systemName: e.target.value})}
                                                    />
                                                </Form.Group>
                                            </Col>
                                            <Col md={6}>
                                                <Form.Group className="mb-3">
                                                    <Form.Label>Timezone</Form.Label>
                                                    <Form.Select
                                                        value={settings.timezone}
                                                        onChange={(e) => setSettings({...settings, timezone: e.target.value})}
                                                    >
                                                        <option value="UTC">UTC</option>
                                                        <option value="EST">Eastern Time</option>
                                                        <option value="PST">Pacific Time</option>
                                                        <option value="GMT">GMT</option>
                                                    </Form.Select>
                                                </Form.Group>
                                            </Col>
                                        </Row>

                                        <Row>
                                            <Col md={6}>
                                                <Form.Group className="mb-3">
                                                    <Form.Label>{t('Data Retention (days)')}</Form.Label>
                                                    <Form.Control
                                                        type="number"
                                                        value={settings.dataRetention}
                                                        onChange={(e) => setSettings({...settings, dataRetention: e.target.value})}
                                                    />
                                                </Form.Group>
                                            </Col>
                                            <Col md={6}>
                                                <Form.Group className="mb-3">
                                                    <Form.Label>{t('Alert Threshold (%)')}</Form.Label>
                                                    <Form.Control
                                                        type="number"
                                                        value={settings.alertThreshold}
                                                        onChange={(e) => setSettings({...settings, alertThreshold: e.target.value})}
                                                    />
                                                </Form.Group>
                                            </Col>
                                        </Row>

                                        <Row>
                                            <Col md={12}>
                                                <h5 className="mb-3">{t('Notification Settings')}</h5>
                                            </Col>
                                        </Row>

                                        <Row>
                                            <Col md={4}>
                                                <Form.Check
                                                    type="switch"
                                                    id="autoBackup"
                                                    label={t('Auto Backup')}
                                                    checked={settings.autoBackup}
                                                    onChange={(e) => setSettings({...settings, autoBackup: e.target.checked})}
                                                />
                                            </Col>
                                            <Col md={4}>
                                                <Form.Check
                                                    type="switch"
                                                    id="emailNotifications"
                                                    label={t('Email Notifications')}
                                                    checked={settings.emailNotifications}
                                                    onChange={(e) => setSettings({...settings, emailNotifications: e.target.checked})}
                                                />
                                            </Col>
                                            <Col md={4}>
                                                <Form.Check
                                                    type="switch"
                                                    id="smsNotifications"
                                                    label={t('SMS Notifications')}
                                                    checked={settings.smsNotifications}
                                                    onChange={(e) => setSettings({...settings, smsNotifications: e.target.checked})}
                                                />
                                            </Col>
                                        </Row>

                                        <Row className="mt-3">
                                            <Col md={12}>
                                                <Button variant="primary" onClick={handleSave}>
                                                    <FeatherIcon icon="save" className="icon-sm me-2" />
                                                    {t('Save Settings')}
                                                </Button>
                                            </Col>
                                        </Row>
                                    </Form>
                                </Card.Body>
                            </Card>
                        </Col>

                        <Col lg={4}>
                            <Card>
                                <Card.Header>
                                    <h5 className="card-title mb-0">{t('System Status')}</h5>
                                </Card.Header>
                                <Card.Body>
                                    <div className="d-flex align-items-center mb-3">
                                        <div className="flex-shrink-0">
                                            <div className="avatar-sm">
                                                <div className="avatar-title bg-success rounded">
                                                    <FeatherIcon icon="check" className="icon-sm text-white" />
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex-grow-1 ms-3">
                                            <h6 className="mb-0">{t('System Online')}</h6>
                                            <small className="text-muted">{t('All services running')}</small>
                                        </div>
                                    </div>

                                    <div className="d-flex align-items-center mb-3">
                                        <div className="flex-shrink-0">
                                            <div className="avatar-sm">
                                                <div className="avatar-title bg-info rounded">
                                                    <FeatherIcon icon="database" className="icon-sm text-white" />
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex-grow-1 ms-3">
                                            <h6 className="mb-0">Database</h6>
                                            <small className="text-muted">{t('Connected')}</small>
                                        </div>
                                    </div>

                                    <div className="d-flex align-items-center mb-3">
                                        <div className="flex-shrink-0">
                                            <div className="avatar-sm">
                                                <div className="avatar-title bg-warning rounded">
                                                    <FeatherIcon icon="wifi" className="icon-sm text-white" />
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex-grow-1 ms-3">
                                            <h6 className="mb-0">Network</h6>
                                            <small className="text-muted">{t('Stable connection')}</small>
                                        </div>
                                    </div>

                                    <div className="d-flex align-items-center">
                                        <div className="flex-shrink-0">
                                            <div className="avatar-sm">
                                                <div className="avatar-title bg-primary rounded">
                                                    <FeatherIcon icon="shield" className="icon-sm text-white" />
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex-grow-1 ms-3">
                                            <h6 className="mb-0">Security</h6>
                                            <small className="text-muted">{t('All checks passed')}</small>
                                        </div>
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
                                            <FeatherIcon icon="refresh-cw" className="icon-sm me-2" />
                                            {t('Restart Services')}
                                        </Button>
                                        <Button variant="outline-warning" size="sm">
                                            <FeatherIcon icon="download" className="icon-sm me-2" />
                                            {t('Backup System')}
                                        </Button>
                                        <Button variant="outline-info" size="sm">
                                            <FeatherIcon icon="settings" className="icon-sm me-2" />
                                            {t('Maintenance Mode')}
                                        </Button>
                                    </div>
                                </Card.Body>
                            </Card>
                        </Col>
                    </Row>
                </Container>
            </div>
        </React.Fragment>
    );
};

SystemSettings.layout = (page: any) => <Layout children={page} />;
export default SystemSettings;
