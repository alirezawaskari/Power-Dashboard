import React from 'react';
import { Card, CardBody, CardHeader, Badge, ListGroup, ListGroupItem } from 'react-bootstrap';
import FeatherIcon from "feather-icons-react";
import { useTranslation } from 'react-i18next';

const AlertSummary = () => {
    const { t } = useTranslation();

    // Mock alert data (replace with API call later)
    const alerts = [
        {
            id: 1,
            type: "critical",
            message: t("Main Panel power threshold exceeded"),
            device: t("Main Electrical Panel"),
            time: t("2 min ago"),
            value: "2,450W"
        },
        {
            id: 2,
            type: "warning",
            message: t("HVAC System efficiency below threshold"),
            device: t("HVAC System"),
            time: t("5 min ago"),
            value: "87%"
        },
        {
            id: 3,
            type: "info",
            message: t("Server Room temperature rising"),
            device: t("Server Room"),
            time: t("8 min ago"),
            value: "28°C"
        },
        {
            id: 4,
            type: "critical",
            message: t("Lighting System offline"),
            device: t("Lighting System"),
            time: t("15 min ago"),
            value: t("Offline")
        }
    ];

    const alertStats = {
        critical: alerts.filter(a => a.type === 'critical').length,
        warning: alerts.filter(a => a.type === 'warning').length,
        info: alerts.filter(a => a.type === 'info').length
    };

    const getAlertColor = (type: string) => {
        switch (type) {
            case 'critical': return 'danger';
            case 'warning': return 'warning';
            case 'info': return 'info';
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
                <h5 className="card-title mb-0">{t('Active Alerts')}</h5>
                <Badge bg="primary" className="rounded-pill">
                    {alerts.length}
                </Badge>
            </CardHeader>
            <CardBody>
                {/* Alert Statistics */}
                <div className="mb-3">
                    <div className="d-flex justify-content-between mb-2">
                        <span className="text-muted">{t('Critical')}</span>
                        <Badge bg="danger">{alertStats.critical}</Badge>
                    </div>
                    <div className="d-flex justify-content-between mb-2">
                        <span className="text-muted">{t('Warning')}</span>
                        <Badge bg="warning">{alertStats.warning}</Badge>
                    </div>
                    <div className="d-flex justify-content-between">
                        <span className="text-muted">{t('Info')}</span>
                        <Badge bg="info">{alertStats.info}</Badge>
                    </div>
                </div>

                <hr />

                {/* Recent Alerts */}
                <div>
                    <h6 className="mb-3">{t('Recent Alerts')}</h6>
                    <ListGroup variant="flush">
                        {alerts.slice(0, 3).map((alert) => (
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
                                            <Badge bg={getAlertColor(alert.type)} size="sm">
                                                {alert.value}
                                            </Badge>
                                        </div>
                                        <p className="mb-1 small text-muted">{alert.message}</p>
                                        <small className="text-muted">{alert.time}</small>
                                    </div>
                                </div>
                            </ListGroupItem>
                        ))}
                    </ListGroup>
                </div>

                <div className="mt-3">
                    <button className="btn btn-outline-primary btn-sm w-100">
                        {t('View All Alerts')}
                    </button>
                </div>
            </CardBody>
        </Card>
    );
};

export default AlertSummary;
