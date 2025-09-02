import React, { useState } from 'react';
import { Card, Col, Dropdown, } from 'react-bootstrap';
import { Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

const TopPages = () => {
    const { t } = useTranslation();
    const [isTopPageDropdown, setTopPageDropdown] = useState<boolean>(false);
    const toggleDropdown = () => { setTopPageDropdown(!isTopPageDropdown); };

    const powerAlerts = [
        { device: t("Main Meter"), alert: t("High Power Consumption"), status: "Critical", time: t("2 min ago") },
        { device: t("Server Room"), alert: t("Voltage Fluctuation"), status: "Warning", time: t("5 min ago") },
        { device: t("Production Floor"), alert: t("Device Offline"), status: "Critical", time: t("15 min ago") },
        { device: t("Office Area"), alert: t("Low Power Factor"), status: "Warning", time: t("1 hour ago") },
        { device: t("Backup Generator"), alert: t("Maintenance Due"), status: "Info", time: t("2 hours ago") },
    ];

    return (
        <React.Fragment>
            <Col xl={4} md={6}>
                <Card className="card-height-100">
                    <Card.Header className="align-items-center d-flex">
                        <h4 className="card-title mb-0 flex-grow-1">{t('Power Alerts')}</h4>
                        <div className="flex-shrink-0">
                            <Dropdown show={isTopPageDropdown} onClick={toggleDropdown} className="card-header-dropdown">
                                <Dropdown.Toggle as="a" className="text-reset dropdown-btn arrow-none" role="button">
                                    <span className="text-muted fs-16"><i className="mdi mdi-dots-vertical align-middle"></i></span>
                                </Dropdown.Toggle>
                                <Dropdown.Menu className="dropdown-menu-end">
                                    <Dropdown.Item>{t('Today')}</Dropdown.Item>
                                    <Dropdown.Item>{t('Last Week')}</Dropdown.Item>
                                    <Dropdown.Item>{t('Last Month')}</Dropdown.Item>
                                    <Dropdown.Item>{t('Current Year')}</Dropdown.Item>
                                </Dropdown.Menu>
                            </Dropdown>
                        </div>
                    </Card.Header>
                    <Card.Body>
                        <div className="table-responsive table-card">
                            <table className="table align-middle table-borderless table-centered table-nowrap mb-0">
                                <thead className="text-muted table-light">
                                    <tr>
                                        <th scope="col" style={{ width: "62" }}>{t('Device')}</th>
                                        <th scope="col">{t('Alert')}</th>
                                        <th scope="col">{t('Status')}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {powerAlerts.map((item, index) => (
                                        <tr key={index}>
                                            <td>
                                                <Link href="#">{item.device}</Link>
                                            </td>
                                            <td>{item.alert}</td>
                                            <td>
                                                <span className={`badge bg-${item.status === 'Critical' ? 'danger' : item.status === 'Warning' ? 'warning' : 'info'}-subtle text-${item.status === 'Critical' ? 'danger' : item.status === 'Warning' ? 'warning' : 'info'}`}>
                                                    {item.status === 'Critical' ? t('Critical') : item.status === 'Warning' ? t('Warning') : t('Info')}
                                                </span>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </Card.Body>
                </Card>
            </Col>
        </React.Fragment>
    );
};

export default TopPages;