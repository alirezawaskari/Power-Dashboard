import React from 'react';
import ReactApexChart from "react-apexcharts";
import useChartColors from "../../Components/Common/useChartColors";
import { useTranslation } from 'react-i18next';

const AudiencesCharts = ({ chartId, series } : any) => {
    const { t } = useTranslation();
    var chartAudienceColumnChartsColors = useChartColors(chartId);
    var options : any = {
        chart: {
            type: 'bar',
            height: 309,
            stacked: true,
            toolbar: {
                show: false,
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '20%',
                borderRadius: 6,
            },
        },
        dataLabels: {
            enabled: false,
        },
        legend: {
            show: true,
            position: 'bottom',
            horizontalAlign: 'center',
            fontWeight: 400,
            fontSize: '8px',
            offsetX: 0,
            offsetY: 0,
            markers: {
                width: 9,
                height: 9,
                radius: 4,
            },
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        grid: {
            show: false,
        },
        colors: chartAudienceColumnChartsColors,
        xaxis: {
            categories: [t('Jan'), t('Feb'), t('Mar'), t('Apr'), t('May'), t('Jun'), t('Jul'), t('Aug'), t('Sep'), t('Oct'), t('Nov'), t('Dec')],
            axisTicks: {
                show: false,
            },
            axisBorder: {
                show: true,
                strokeDashArray: 1,
                height: 1,
                width: '100%',
                offsetX: 0,
                offsetY: 0
            },
        },
        yaxis: {
            show: false
        },
        fill: {
            opacity: 1
        }
    };
    return (
        <React.Fragment>
            <ReactApexChart dir="ltr"
                options={options}
                series={series}
                id={chartId}
                data-colors='["--vz-success", "--vz-light"]' 
                data-colors-minimal='["--vz-primary", "--vz-light"]' 
                data-colors-modern='["--vz-primary", "--vz-light"]' 
                data-colors-interactive='["--vz-primary", "--vz-light"]' 
                data-colors-creative='["--vz-secondary", "--vz-light"]' 
                data-colors-corporate='["--vz-primary", "--vz-light"]' 
                data-colors-galaxy='["--vz-primary", "--vz-light"]' 
                data-colors-classic='["--vz-primary", "--vz-secondary"]' 
                data-colors-vintage='["--vz-primary", "--vz-success-rgb, 0.5"]'
                type="bar"
                height="309"
                className="apex-charts"
            />
        </React.Fragment>
    );
};

const AudiencesSessionsCharts = ({ chartId, series } : any) => {
    const { t } = useTranslation();
    var chartHeatMapBasicColors = useChartColors(chartId);

    var options : any = {
        chart: {
            height: 400,
            type: 'heatmap',
            offsetX: 0,
            offsetY: -8,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            heatmap: {
                colorScale: {
                    ranges: [{
                        from: 0,
                        to: 50,
                        color: chartHeatMapBasicColors[0]
                    },
                    {
                        from: 51,
                        to: 100,
                        color: chartHeatMapBasicColors[1]
                    },
                    ],
                },

            }
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            show: true,
            horizontalAlign: 'center',
            offsetX: 0,
            offsetY: 20,
            markers: {
                width: 20,
                height: 6,
                radius: 2,
            },
            itemMargin: {
                horizontal: 12,
                vertical: 0
            },
        },
        colors: chartHeatMapBasicColors,
        tooltip: {
            y: [{
                title: {
                    formatter: function (val : any) {
                        return val + " " + t("(mins)")
                    }
                }
            }, {
                title: {
                    formatter: function (val : any) {
                        return val + " " + t("per session")
                    }
                }
            }, {
                title: {
                    formatter: function (val : any) {
                        return val;
                    }
                }
            }]
        }
    };
    return (
        <React.Fragment>
            <ReactApexChart dir="ltr"
                options={options}
                series={series}
                id={chartId}
                data-colors='["--vz-primary", "--vz-success"]' 
                data-colors-minimal='["--vz-primary", "--vz-primary-rgb, 0.85"]' 
                data-colors-material='["--vz-primary", "--vz-success"]' 
                data-colors-galaxy='["--vz-primary", "--vz-primary-rgb, 0.85"]' 
                data-colors-classic='["--vz-primary", "--vz-success"]'
                type="heatmap"
                height="400"
                className="apex-charts"
            />
        </React.Fragment>
    );
};

const CountriesCharts = ({ chartId, series } : any) => {
    const { t } = useTranslation();
    var chartCountriesColors = useChartColors(chartId);
    const options : any = {
        chart: {
            type: 'bar',
            height: 436,
            toolbar: {
                show: false,
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            title: {
                text: t('Countries')
            },
        },
        yaxis: {
            title: {
                text: t('Sessions')
            },
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val : any) {
                    return val + " " + t("Sessions")
                }
            }
        },
        legend: {
            show: false,
        },
        grid: {
            show: false,
        },
        xaxis: {
            categories: [t('India'), t('United States'), t('China'), t('Indonesia'), t('Russia'), t('Bangladesh'), t('Canada'), t('Brazil'), t('Vietnam'), t('UK')],
        },
    };
    return (
        <React.Fragment>
            <ReactApexChart dir="ltr"
                options={options}
                series={series}
                id={chartId}
                type="bar"
                height="436"
                data-colors='["--vz-info", "--vz-info", "--vz-info", "--vz-info", "--vz-danger", "--vz-info", "--vz-info", "--vz-info", "--vz-info", "--vz-info"]' 
                data-colors-minimal='["--vz-primary", "--vz-primary", "--vz-primary", "--vz-primary", "--vz-primary-rgb, 0.45", "--vz-primary", "--vz-primary", "--vz-primary", "--vz-primary", "--vz-primary"]' 
                data-colors-material='["--vz-primary", "--vz-primary", "--vz-info", "--vz-info", "--vz-danger", "--vz-primary", "--vz-primary", "--vz-warning", "--vz-primary", "--vz-primary"]' 
                data-colors-galaxy='["--vz-primary-rgb, 0.4", "--vz-primary-rgb, 0.4", "--vz-primary-rgb, 0.4", "--vz-primary-rgb, 0.4", "--vz-primary", "--vz-primary-rgb, 0.4", "--vz-primary-rgb, 0.4", "--vz-primary-rgb, 0.4", "--vz-primary-rgb, 0.4", "--vz-primary-rgb, 0.4"]' 
                data-colors-classic='["--vz-primary", "--vz-primary", "--vz-primary", "--vz-primary", "--vz-primary-rgb, 0.45", "--vz-primary", "--vz-primary", "--vz-primary", "--vz-primary", "--vz-primary"]'
                className="apex-charts"
            />
        </React.Fragment>
    );
};

const UsersByDeviceCharts = ({ chartId, series } : any) => {
    const { t } = useTranslation();
    var dountchartUserDeviceColors = useChartColors(chartId);
    const options : any = {
        labels: [t("Desktop"), t("Mobile"), t("Tablet")],
        chart: {
            type: "donut",
            height: 219,
        },
        plotOptions: {
            pie: {
                size: 100,
                donut: {
                    size: "76%",
                },
            },
        },
        dataLabels: {
            enabled: false,
        },
        legend: {
            show: false,
            position: 'bottom',
            horizontalAlign: 'center',
            offsetX: 0,
            offsetY: 0,
            markers: {
                width: 20,
                height: 6,
                radius: 2,
            },
            itemMargin: {
                horizontal: 12,
                vertical: 0
            },
        },
        stroke: {
            width: 0
        },
        yaxis: {
            labels: {
                formatter: function (value : any) {
                    return value + t('k Users');
                }
            },
            tickAmount: 4,
            min: 0
        },
        colors: dountchartUserDeviceColors,
    };
    return (
        <React.Fragment>
            <ReactApexChart dir="ltr"
                options={options}
                series={series}
                id={chartId}
                data-colors='["--vz-primary", "--vz-warning", "--vz-info"]' 
                data-colors-minimal='["--vz-primary", "--vz-primary-rgb, 0.60", "--vz-primary-rgb, 0.75"]' 
                data-colors-galaxy='["--vz-primary", "--vz-primary-rgb, .75", "--vz-primary-rgb, 0.60"]'
                type="donut"
                height="219"
                className="apex-charts"
            />
        </React.Fragment>
    );
};


export { AudiencesCharts, AudiencesSessionsCharts, CountriesCharts, UsersByDeviceCharts };