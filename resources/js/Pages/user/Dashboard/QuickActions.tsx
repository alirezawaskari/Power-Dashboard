import React from 'react';
import { Card, CardBody, CardHeader, Button, Row, Col } from 'react-bootstrap';
import FeatherIcon from "feather-icons-react";
import { useTranslation } from 'react-i18next';

const QuickActions = () => {
    const { t } = useTranslation();
    const actions = [
        {
            id: 1,
            title: t("View All Devices"),
            icon: "server",
            color: "primary",
            action: () => console.log("View all devices")
        },
        {
            id: 2,
            title: t("Generate Report"),
            icon: "file-text",
            color: "success",
            action: () => console.log("Generate report")
        },
        {
            id: 3,
            title: t("Configure Alerts"),
            icon: "bell",
            color: "warning",
            action: () => console.log("Configure alerts")
        },
        {
            id: 4,
            title: t("System Settings"),
            icon: "settings",
            color: "info",
            action: () => console.log("System settings")
        },
        {
            id: 5,
            title: t("Support Ticket"),
            icon: "help-circle",
            color: "secondary",
            action: () => console.log("Create support ticket")
        },
        {
            id: 6,
            title: t("Export Data"),
            icon: "download",
            color: "dark",
            action: () => console.log("Export data")
        }
    ];

    return (
        <Card>
            <CardHeader>
                <h5 className="card-title mb-0">{t('Quick Actions')}</h5>
            </CardHeader>
            <CardBody>
                <Row>
                    {actions.map((action) => (
                        <Col sm={6} key={action.id} className="mb-3">
                            <Button
                                variant={`outline-${action.color}`}
                                className="w-100 d-flex align-items-center justify-content-start"
                                onClick={action.action}
                                size="sm"
                            >
                                <FeatherIcon 
                                    icon={action.icon} 
                                    size={16} 
                                    className="me-2"
                                />
                                <span className="small">{action.title}</span>
                            </Button>
                        </Col>
                    ))}
                </Row>
            </CardBody>
        </Card>
    );
};

export default QuickActions;
