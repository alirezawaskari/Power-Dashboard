import React from 'react';
import { Card, CardBody, CardHeader, Badge, Button, ListGroup, ListGroupItem, Row, Col } from 'react-bootstrap';
import FeatherIcon from "feather-icons-react";
import { useTranslation } from 'react-i18next';

const AlertManagement = () => {
    const { t } = useTranslation();
    // Mock alert data (replace with API call later)
    const alerts = [
        {
            id: 1,
            type: "critical",
            message: t("Main Panel power threshold exceeded"),
            device: t("Main Electrical Panel"),
            time: t("2 min ago"),
            status: "active",
            priority: "high"
        },
        {
            id: 2,
            type: "warning",
            message: t("HVAC System efficiency below threshold"),
            device: t("HVAC System"),
            time: t("5 min ago"),
            status: "acknowledged",
            priority: "medium"
        },
        {
            id: 3,
            type: "info",
            message: t("Server Room temperature rising"),
            device: t("Server Room"),
            time: t("8 min ago"),
            status: "resolved",
            priority: "low"
        },
        {
            id: 4,
            type: "critical",
            message: t("Lighting System offline"),
            device: t("Lighting System"),
            time: t("15 min ago"),
            status: "active",
            priority: "high"
        },
        {
            id: 5,
            type: "warning",
            message: t("Database connection slow"),
            device: t("Database Server"),
            time: t("20 min ago"),
            status: "active",
            priority: "medium"
        }
    ];

    const alertStats = {
        total: alerts.length,
        active: alerts.filter(a => a.status === 'active').length,
        acknowledged: alerts.filter(a => a.status === 'acknowledged').length,
        resolved: alerts.filter(a => a.status === 'resolved').length
    };

    const getAlertColor = (type: string) => {
        switch (type) {
            case 'critical': return 'danger';
            case 'warning': return 'warning';
            case 'info': return 'info';
            default: return 'secondary';
        }
    };

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'active': return 'danger';
            case 'acknowledged': return 'warning';
            case 'resolved': return 'success';
            default: return 'secondary';
        }
    };

    const getAlertIcon = (type: string) => {
        switch (type) {
            case 'critical': return 'alert-triangle';
            case 'warning': return 'alert-circle';
            case 'info': return 'info';
            default: return 'bell';
        }
    };

    return (
        <Card>
            <CardHeader className="d-flex justify-content-between align-items-center">
                <h4 className="card-title mb-0">{t('Alert Management')}</h4>
                <Button variant="primary" size="sm">
                    <FeatherIcon icon="settings" size={16} className="me-1" />
                    {t('Configure')}
                </Button>
            </CardHeader>
            <CardBody>
                {/* Alert Statistics */}
                <Row className="mb-3">
                    <Col sm={3}>
                        <div className="text-center p-2">
                            <h5 className="mb-1 text-primary">{alertStats.total}</h5>
                            <small className="text-muted">{t('Total')}</small>
                        </div>
                    </Col>
                    <Col sm={3}>
                        <div className="text-center p-2">
                            <h5 className="mb-1 text-danger">{alertStats.active}</h5>
                            <small className="text-muted">{t('Active')}</small>
                        </div>
                    </Col>
                    <Col sm={3}>
                        <div className="text-center p-2">
                            <h5 className="mb-1 text-warning">{alertStats.acknowledged}</h5>
                            <small className="text-muted">{t('Acknowledged')}</small>
                        </div>
                    </Col>
                    <Col sm={3}>
                        <div className="text-center p-2">
                            <h5 className="mb-1 text-success">{alertStats.resolved}</h5>
                            <small className="text-muted">{t('Resolved')}</small>
                        </div>
                    </Col>
                </Row>

                <hr />

                {/* Recent Alerts */}
                <div>
                    <h6 className="mb-3">{t('Recent Alerts')}</h6>
                    <ListGroup variant="flush">
                        {alerts.slice(0, 4).map((alert) => (
                            <ListGroupItem key={alert.id} className="px-0 py-2">
                                <div className="d-flex align-items-start">
                                    <div className="flex-shrink-0 me-2">
                                        <FeatherIcon 
                                            icon={getAlertIcon(alert.type)} 
                                            size={16} 
                                            className={`text-${getAlertColor(alert.type)}`}
                                        />
                                    </div>
                                    <div className="flex-grow-1">
                                        <div className="d-flex justify-content-between align-items-start mb-1">
                                            <h6 className="mb-0 small">{alert.device}</h6>
                                            <div className="d-flex gap-1">
                                                <Badge bg={getStatusColor(alert.status)} size="sm">
                                                    {alert.status === 'active' ? t('Active') :
                                                     alert.status === 'acknowledged' ? t('Acknowledged') :
                                                     alert.status === 'resolved' ? t('Resolved') : alert.status}
                                                </Badge>
                                                <Badge bg={getAlertColor(alert.type)} size="sm">
                                                    {alert.priority === 'high' ? t('High') :
                                                     alert.priority === 'medium' ? t('Medium') :
                                                     alert.priority === 'low' ? t('Low') : alert.priority}
                                                </Badge>
                                            </div>
                                        </div>
                                        <p className="mb-1 small text-muted">{alert.message}</p>
                                        <div className="d-flex justify-content-between align-items-center">
                                            <small className="text-muted">{alert.time}</small>
                                            <div className="btn-group btn-group-sm">
                                                <Button variant="outline-primary" size="sm">
                                                    <FeatherIcon icon="check" size={12} />
                                                </Button>
                                                <Button variant="outline-success" size="sm">
                                                    <FeatherIcon icon="check-circle" size={12} />
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </ListGroupItem>
                        ))}
                    </ListGroup>
                </div>

                <div className="mt-3">
                    <Button variant="outline-primary" size="sm" className="w-100">
                        {t('View All Alerts')}
                    </Button>
                </div>
            </CardBody>
        </Card>
    );
};

export default AlertManagement;
