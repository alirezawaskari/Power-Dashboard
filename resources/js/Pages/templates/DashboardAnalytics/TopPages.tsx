import React, { useState } from 'react';
import { Card, Col, Dropdown, } from 'react-bootstrap';
import { Link } from '@inertiajs/react';

const TopPages = () => {
    const [isTopPageDropdown, setTopPageDropdown] = useState<boolean>(false);
    const toggleDropdown = () => { setTopPageDropdown(!isTopPageDropdown); };

    const powerAlerts = [
        { device: "Main Meter", alert: "High Power Consumption", status: "Critical", time: "2 min ago" },
        { device: "Server Room", alert: "Voltage Fluctuation", status: "Warning", time: "5 min ago" },
        { device: "Production Floor", alert: "Device Offline", status: "Critical", time: "15 min ago" },
        { device: "Office Area", alert: "Low Power Factor", status: "Warning", time: "1 hour ago" },
        { device: "Backup Generator", alert: "Maintenance Due", status: "Info", time: "2 hours ago" },
    ];

    return (
        <React.Fragment>
            <Col xl={4} md={6}>
                <Card className="card-height-100">
                    <Card.Header className="align-items-center d-flex">
                        <h4 className="card-title mb-0 flex-grow-1">Power Alerts</h4>
                        <div className="flex-shrink-0">
                            <Dropdown show={isTopPageDropdown} onClick={toggleDropdown} className="card-header-dropdown">
                                <Dropdown.Toggle as="a" className="text-reset dropdown-btn arrow-none" role="button">
                                    <span className="text-muted fs-16"><i className="mdi mdi-dots-vertical align-middle"></i></span>
                                </Dropdown.Toggle>
                                <Dropdown.Menu className="dropdown-menu-end">
                                    <Dropdown.Item>Today</Dropdown.Item>
                                    <Dropdown.Item>Last Week</Dropdown.Item>
                                    <Dropdown.Item>Last Month</Dropdown.Item>
                                    <Dropdown.Item>Current Year</Dropdown.Item>
                                </Dropdown.Menu>
                            </Dropdown>
                        </div>
                    </Card.Header>
                    <Card.Body>
                        <div className="table-responsive table-card">
                            <table className="table align-middle table-borderless table-centered table-nowrap mb-0">
                                <thead className="text-muted table-light">
                                    <tr>
                                        <th scope="col" style={{ width: "62" }}>Device</th>
                                        <th scope="col">Alert</th>
                                        <th scope="col">Status</th>
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
                                                    {item.status}
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