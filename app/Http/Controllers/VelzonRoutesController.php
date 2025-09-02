<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class VelzonRoutesController extends Controller
{
    // dashboard

    public function index()
    {
        return Inertia::render('templates/DashboardEcommerce/index');
    }

    public function dashboard_analytics()
    {
        return Inertia::render('templates/DashboardAnalytics/index');
    }

    public function dashboard_crm()
    {
        return Inertia::render('templates/DashboardCrm/index');
    }

    public function dashboard()
    {
        return Inertia::render('templates/DashboardEcommerce/index');
    }

    public function dashboard_crypto()
    {
        return Inertia::render('templates/DashboardCrypto/index');
    }

    public function dashboard_projects()
    {
        return Inertia::render('templates/DashboardProject/index');
    }

    public function dashboard_nft()
    {
        return Inertia::render('templates/DashboardNFT/index');
    }

    public function dashboard_job()
    {
        return Inertia::render('templates/DashboardJob/index');
    }

    public function dashboard_blog()
    {
        return Inertia::render('templates/DashboardBlog/index');
    }

    // apps

    public function apps_calendar()
    {
        return Inertia::render('templates/Calendar/index');
    }

    public function apps_calendar_month_grid()
    {
        return Inertia::render('templates/Calendar/monthGrid');
    }

    public function apps_ecommerce_products()
    {
        return Inertia::render('templates/Ecommerce/EcommerceProducts/index');
    }

    public function apps_ecommerce_product_details()
    {
        return Inertia::render('templates/Ecommerce/EcommerceProducts/EcommerceProductDetail');
    }

    public function apps_ecommerce_add_product()
    {
        return Inertia::render('templates/Ecommerce/EcommerceProducts/EcommerceAddProduct');
    }

    public function apps_ecommerce_order_details()
    {
        return Inertia::render('templates/Ecommerce/EcommerceOrders/EcommerceOrderDetail');
    }

    public function apps_ecommerce_customers()
    {
        return Inertia::render('templates/Ecommerce/EcommerceCustomers/index');
    }

    public function apps_ecommerce_cart()
    {
        return Inertia::render('templates/Ecommerce/EcommerceCart');
    }

    public function apps_ecommerce_checkout()
    {
        return Inertia::render('templates/Ecommerce/EcommerceCheckout');
    }

    public function apps_ecommerce_sellers()
    {
        return Inertia::render('templates/Ecommerce/EcommerceSellers/index');
    }

    public function apps_ecommerce_seller_details()
    {
        return Inertia::render('templates/Ecommerce/EcommerceSellers/EcommerceSellerDetail');
    }

    public function apps_file_manager()
    {
        return Inertia::render('templates/FileManager/index');
    }

    public function apps_todo()
    {
        return Inertia::render('templates/ToDo/index');
    }

    public function apps_chat()
    {
        return Inertia::render('templates/Chat/index');
    }

    public function apps_mailbox()
    {
        return Inertia::render('templates/EmailInbox/index');
    }

    public function apps_email_basic()
    {
        return Inertia::render('templates/Email/EmailTemplates/BasicAction/index');
    }

    public function apps_email_ecommerce()
    {
        return Inertia::render('templates/Email/EmailTemplates/EcommerceAction/index');
    }

    public function apps_projects_list()
    {
        return Inertia::render('templates/Projects/ProjectList/index');
    }

    public function apps_projects_overview()
    {
        return Inertia::render('templates/Projects/ProjectOverview/index');
    }

    public function apps_projects_create()
    {
        return Inertia::render('templates/Projects/CreateProject/index');
    }

    public function apps_tasks_list_view()
    {
        return Inertia::render('templates/Tasks/TaskList/index');
    }

    public function apps_tasks_details()
    {
        return Inertia::render('templates/Tasks/TaskDetails/index');
    }

    public function apps_tasks_kanban()
    {
        return Inertia::render('templates/Tasks/KanbanBoard/index');
    }

    public function apps_api_key()
    {
        return Inertia::render('templates/APIKey/index');
    }

    public function apps_crm_contacts()
    {
        return Inertia::render('templates/Crm/CrmContacts');
    }

    public function apps_crm_companies()
    {
        return Inertia::render('templates/Crm/CrmCompanies');
    }

    public function apps_crm_deals()
    {
        return Inertia::render('templates/Crm/CrmDeals/index');
    }

    public function apps_crm_leads()
    {
        return Inertia::render('templates/Crm/CrmLeads/index');
    }

    public function apps_invoices_list()
    {
        return Inertia::render('templates/Invoices/InvoiceList');
    }

    public function apps_invoices_details()
    {
        return Inertia::render('templates/Invoices/InvoiceDetails');
    }

    public function apps_invoices_create()
    {
        return Inertia::render('templates/Invoices/InvoiceCreate');
    }

    public function apps_tickets_list()
    {
        return Inertia::render('templates/SupportTickets/ListView/index');
    }

    public function apps_tickets_details()
    {
        return Inertia::render('templates/SupportTickets/TicketsDetails/index');
    }

    public function apps_crypto_transactions()
    {
        return Inertia::render('templates/Crypto/Transactions/index');
    }

    public function apps_crypto_buy_sell()
    {
        return Inertia::render('templates/Crypto/BuySell/index');
    }

    public function apps_crypto_orders()
    {
        return Inertia::render('templates/Crypto/CryptoOrder/index');
    }

    public function apps_crypto_wallet()
    {
        return Inertia::render('templates/Crypto/MyWallet/index');
    }

    public function apps_crypto_ico()
    {
        return Inertia::render('templates/Crypto/ICOList/index');
    }

    public function apps_crypto_kyc()
    {
        return Inertia::render('templates/Crypto/KYCVerification/index');
    }

    public function apps_nft_marketplace()
    {
        return Inertia::render('templates/NFTMarketplace/Marketplace/index');
    }

    public function apps_nft_collections()
    {
        return Inertia::render('templates/NFTMarketplace/Collections/index');
    }

    public function apps_nft_create()
    {
        return Inertia::render('templates/NFTMarketplace/CreateNFT/index');
    }

    public function apps_nft_creators()
    {
        return Inertia::render('templates/NFTMarketplace/Creators/index');
    }

    public function apps_nft_explore()
    {
        return Inertia::render('templates/NFTMarketplace/ExploreNow/index');
    }

    public function apps_nft_item_details()
    {
        return Inertia::render('templates/NFTMarketplace/Itemdetails/index');
    }

    public function apps_nft_auction()
    {
        return Inertia::render('templates/NFTMarketplace/LiveAuction/index');
    }

    public function apps_nft_ranking()
    {
        return Inertia::render('templates/NFTMarketplace/Ranking/index');
    }

    public function apps_nft_wallet()
    {
        return Inertia::render('templates/NFTMarketplace/WalletConnect/index');
    }

    public function apps_job_statistics()
    {
        return Inertia::render('templates/Jobs/Statistics/index');
    }

    public function apps_job_lists()
    {
        return Inertia::render('templates/Jobs/JobList/List/index');
    }

    public function apps_job_grid_lists()
    {
        return Inertia::render('templates/Jobs/JobList/Grid/index');
    }

    public function apps_job_details()
    {
        return Inertia::render('templates/Jobs/JobList/Overview/index');
    }

    public function apps_job_candidate_lists()
    {
        return Inertia::render('templates/Jobs/CandidateList/ListView/index');
    }

    public function apps_job_candidate_grid()
    {
        return Inertia::render('templates/Jobs/CandidateList/GridView/index');
    }

    public function apps_job_application()
    {
        return Inertia::render('templates/Jobs/Application/index');
    }

    public function apps_job_new()
    {
        return Inertia::render('templates/Jobs/NewJob/index');
    }

    public function apps_job_companies_lists()
    {
        return Inertia::render('templates/Jobs/CompaniesList/index');
    }

    public function apps_job_categories()
    {
        return Inertia::render('templates/Jobs/JobCategories/index');
    }

    // charts

    public function charts_apex_line()
    {
        return Inertia::render('templates/Charts/ApexCharts/LineCharts/index');
    }

    public function charts_apex_area()
    {
        return Inertia::render('templates/Charts/ApexCharts/AreaCharts/index');
    }

    public function charts_apex_column()
    {
        return Inertia::render('templates/Charts/ApexCharts/ColumnCharts/index');
    }

    public function charts_apex_bar()
    {
        return Inertia::render('templates/Charts/ApexCharts/BarCharts/index');
    }

    public function charts_apex_mixed()
    {
        return Inertia::render('templates/Charts/ApexCharts/MixedCharts/index');
    }

    public function charts_apex_timeline()
    {
        return Inertia::render('templates/Charts/ApexCharts/TimelineCharts/index');
    }

    public function charts_apex_range_area()
    {
        return Inertia::render('templates/Charts/ApexCharts/RangeAreaCharts/Index');
    }

    public function charts_apex_funnel()
    {
        return Inertia::render('templates/Charts/ApexCharts/FunnelCharts/Index');
    }

    public function charts_apex_candlestick()
    {
        return Inertia::render('templates/Charts/ApexCharts/CandlestickChart/index');
    }

    public function charts_apex_boxplot()
    {
        return Inertia::render('templates/Charts/ApexCharts/BoxplotCharts/index');
    }

    public function charts_apex_bubble()
    {
        return Inertia::render('templates/Charts/ApexCharts/BubbleChart/index');
    }

    public function charts_apex_scatter()
    {
        return Inertia::render('templates/Charts/ApexCharts/ScatterCharts/index');
    }

    public function charts_apex_heatmap()
    {
        return Inertia::render('templates/Charts/ApexCharts/HeatmapCharts/index');
    }

    public function charts_apex_treemap()
    {
        return Inertia::render('templates/Charts/ApexCharts/TreemapCharts/index');
    }

    public function charts_apex_pie()
    {
        return Inertia::render('templates/Charts/ApexCharts/PieCharts/index');
    }

    public function charts_apex_radialbar()
    {
        return Inertia::render('templates/Charts/ApexCharts/RadialbarCharts/index');
    }

    public function charts_apex_radar()
    {
        return Inertia::render('templates/Charts/ApexCharts/RadarCharts/index');
    }

    public function charts_apex_polar()
    {
        return Inertia::render('templates/Charts/ApexCharts/PolarCharts/index');
    }

    public function charts_apex_slope()
    {
        return Inertia::render('templates/Charts/ApexCharts/SlopeCharts/index');
    }

    public function charts_chartjs()
    {
        return Inertia::render('templates/Charts/ChartsJs/index');
    }

    public function charts_echarts()
    {
        return Inertia::render('templates/Charts/ECharts/index');
    }

    // ui

    public function ui_alerts()
    {
        return Inertia::render('templates/BaseUi/UiAlerts/UiAlerts');
    }

    public function ui_badges()
    {
        return Inertia::render('templates/BaseUi/UiBadges/UiBadges');
    }

    public function ui_buttons()
    {
        return Inertia::render('templates/BaseUi/UiButtons/UiButtons');
    }

    public function ui_colors()
    {
        return Inertia::render('templates/BaseUi/UiColors/UiColors');
    }

    public function ui_cards()
    {
        return Inertia::render('templates/BaseUi/UiCards/UiCards');
    }

    public function ui_carousel()
    {
        return Inertia::render('templates/BaseUi/UiCarousel/UiCarousel');
    }

    public function ui_dropdowns()
    {
        return Inertia::render('templates/BaseUi/UiDropdowns/UiDropdowns');
    }

    public function ui_grid()
    {
        return Inertia::render('templates/BaseUi/UiGrid/UiGrid');
    }

    public function ui_images()
    {
        return Inertia::render('templates/BaseUi/UiImages/UiImages');
    }

    public function ui_tabs()
    {
        return Inertia::render('templates/BaseUi/UiTabs/UiTabs');
    }

    public function ui_accordions()
    {
        return Inertia::render('templates/BaseUi/UiAccordion&Collapse/UiAccordion&Collapse');
    }

    public function ui_modals()
    {
        return Inertia::render('templates/BaseUi/UiModals/UiModals');
    }

    public function ui_offcanvas()
    {
        return Inertia::render('templates/BaseUi/UiOffcanvas/UiOffcanvas');
    }

    public function ui_placeholders()
    {
        return Inertia::render('templates/BaseUi/UiPlaceholders/UiPlaceholders');
    }

    public function ui_progress()
    {
        return Inertia::render('templates/BaseUi/UiProgress/UiProgress');
    }

    public function ui_notifications()
    {
        return Inertia::render('templates/BaseUi/UiNotifications/UiNotifications');
    }

    public function ui_media()
    {
        return Inertia::render('templates/BaseUi/UiMediaobject/UiMediaobject');
    }

    public function ui_embed_video()
    {
        return Inertia::render('templates/BaseUi/UiEmbedVideo/UiEmbedVideo');
    }

    public function ui_typography()
    {
        return Inertia::render('templates/BaseUi/UiTypography/UiTypography');
    }

    public function ui_lists()
    {
        return Inertia::render('templates/BaseUi/UiLists/UiLists');
    }

    public function ui_links()
    {
        return Inertia::render('templates/BaseUi/UiLinks/UiLinks');
    }

    public function ui_general()
    {
        return Inertia::render('templates/BaseUi/UiGeneral/UiGeneral');
    }

    public function ui_ribbons()
    {
        return Inertia::render('templates/BaseUi/UiRibbons/UiRibbons');
    }

    public function ui_utilities()
    {
        return Inertia::render('templates/BaseUi/UiUtilities/UiUtilities');
    }

    // advanced-ui

    public function advance_ui_scrollbar()
    {
        return Inertia::render('templates/AdvanceUi/UiScrollbar/UiScrollbar');
    }

    public function advance_ui_swiper()
    {
        return Inertia::render('templates/AdvanceUi/UiSwiperSlider/UiSwiperSlider');
    }

    public function advance_ui_ratings()
    {
        return Inertia::render('templates/AdvanceUi/UiRatings/UiRatings');
    }

    public function advance_ui_highlight()
    {
        return Inertia::render('templates/AdvanceUi/UiHighlight/UiHighlight');
    }

    // widgets

    public function widgets()
    {
        return Inertia::render('templates/Widgets/Index');
    }

    // forms

    public function forms_elements()
    {
        return Inertia::render('templates/Forms/BasicElements/BasicElements');
    }

    public function forms_select()
    {
        return Inertia::render('templates/Forms/FormSelect/FormSelect');
    }

    public function forms_checkboxes_radios()
    {
        return Inertia::render('templates/Forms/CheckboxAndRadio/CheckBoxAndRadio');
    }

    public function forms_pickers()
    {
        return Inertia::render('templates/Forms/FormPickers/FormPickers');
    }

    public function forms_masks()
    {
        return Inertia::render('templates/Forms/Masks/Masks');
    }

    public function forms_advanced()
    {
        return Inertia::render('templates/Forms/FormAdvanced/FormAdvanced');
    }

    public function forms_range_sliders()
    {
        return Inertia::render('templates/Forms/FormRangeSlider/FormRangeSlider');
    }

    public function forms_validation()
    {
        return Inertia::render('templates/Forms/FormValidation/FormValidation');
    }

    public function forms_wizard()
    {
        return Inertia::render('templates/Forms/FormWizard/FormWizard');
    }

    public function forms_editors()
    {
        return Inertia::render('templates/Forms/FormEditor/FormEditor');
    }

    public function forms_file_uploads()
    {
        return Inertia::render('templates/Forms/FileUpload/FileUpload');
    }

    public function forms_layouts()
    {
        return Inertia::render('templates/Forms/FormLayouts/Formlayouts');
    }

    public function forms_select2()
    {
        return Inertia::render('templates/Forms/Select2/Select2');
    }

    // tables

    public function tables_basic()
    {
        return Inertia::render('templates/Tables/BasicTables/BasicTables');
    }

    public function tables_react()
    {
        return Inertia::render('templates/Tables/ReactTables/index');
    }


    // icons

    public function icons_remix()
    {
        return Inertia::render('templates/Icons/RemixIcons/RemixIcons');
    }

    public function icons_boxicons()
    {
        return Inertia::render('templates/Icons/BoxIcons/BoxIcons');
    }

    public function icons_materialdesign()
    {
        return Inertia::render('templates/Icons/MaterialDesign/MaterialDesign');
    }

    public function icons_feather()
    {
        return Inertia::render('templates/Icons/FeatherIcons/FeatherIcons');
    }

    public function icons_lineawebsome()
    {
        return Inertia::render('templates/Icons/LineAwesomeIcons/LineAwesomeIcons');
    }

    public function icons_crypto()
    {
        return Inertia::render('templates/Icons/CryptoIcons/CryptoIcons');
    }

    // map

    public function maps_google()
    {
        return Inertia::render('templates/Maps/GoogleMaps');
    }

    // pages

    public function pages_starter()
    {
        return Inertia::render('templates/Pages/Starter/Starter');
    }

    public function pages_profile()
    {
        return Inertia::render('templates/Pages/Profile/SimplePage/SimplePage');
    }

    public function pages_profile_settings()
    {
        return Inertia::render('templates/Pages/Profile/Settings/Settings');
    }

    public function pages_team()
    {
        return Inertia::render('templates/Pages/Team/Team');
    }

    public function pages_timeline()
    {
        return Inertia::render('templates/Pages/Timeline/Timeline');
    }

    public function pages_faqs()
    {
        return Inertia::render('templates/Pages/Faqs/Faqs');
    }

    public function pages_gallery()
    {
        return Inertia::render('templates/Pages/Gallery/Gallery');
    }

    public function pages_pricing()
    {
        return Inertia::render('templates/Pages/Pricing/Pricing');
    }

    public function pages_search_results()
    {
        return Inertia::render('templates/Pages/SearchResults/SearchResults');
    }

    public function pages_sitemap()
    {
        return Inertia::render('templates/Pages/SiteMap/SiteMap');
    }

    public function pages_privacy_policy()
    {
        return Inertia::render('templates/Pages/PrivacyPolicy/PrivacyPolicy');
    }

    public function pages_terms_condition()
    {
        return Inertia::render('templates/Pages/TermsCondition/TermsCondition');
    }

    public function pages_blog_grid()
    {
        return Inertia::render('templates/Pages/Blogs/GridView/index');
    }

    public function pages_blog_list()
    {
        return Inertia::render('templates/Pages/Blogs/ListView/index');
    }

    public function pages_blog_overview()
    {
        return Inertia::render('templates/Pages/Blogs/Overview/index');
    }

    public function pages_maintenance()
    {
        return Inertia::render('templates/Pages/Maintenance/Maintenance');
    }

    public function pages_coming_soon()
    {
        return Inertia::render('templates/Pages/ComingSoon/ComingSoon');
    }

    // auth inner

    public function auth_signin_basic()
    {
        return Inertia::render('templates/AuthInner/Login/BasicSignIn');
    }

    public function auth_signin_cover()
    {
        return Inertia::render('templates/AuthInner/Login/CoverSignIn');
    }

    public function auth_signup_basic()
    {
        return Inertia::render('templates/AuthInner/Register/BasicSignUp');
    }

    public function auth_signup_cover()
    {
        return Inertia::render('templates/AuthInner/Register/CoverSignUp');
    }

    public function auth_pass_reset_basic()
    {
        return Inertia::render('templates/AuthInner/PasswordReset/BasicPasswReset');
    }

    public function auth_pass_reset_cover()
    {
        return Inertia::render('templates/AuthInner/PasswordReset/CoverPasswReset');
    }

    public function auth_lockscreen_basic()
    {
        return Inertia::render('templates/AuthInner/LockScreen/BasicLockScr');
    }

    public function auth_lockscreen_cover()
    {
        return Inertia::render('templates/AuthInner/LockScreen/CoverLockScr');
    }

    public function auth_logout_basic()
    {
        return Inertia::render('templates/AuthInner/Logout/BasicLogout');
    }

    public function auth_logout_cover()
    {
        return Inertia::render('templates/AuthInner/Logout/CoverLogout');
    }

    public function auth_success_msg_basic()
    {
        return Inertia::render('templates/AuthInner/SuccessMessage/BasicSuccessMsg');
    }

    public function auth_success_msg_cover()
    {
        return Inertia::render('templates/AuthInner/SuccessMessage/CoverSuccessMsg');
    }

    public function auth_twostep_basic()
    {
        return Inertia::render('templates/AuthInner/TwoStepVerification/BasicTwosVerify');
    }

    public function auth_twostep_cover()
    {
        return Inertia::render('templates/AuthInner/TwoStepVerification/CoverTwosVerify');
    }

    public function auth_404_basic()
    {
        return Inertia::render('templates/AuthInner/Error/Basic404');
    }

    public function auth_404_cover()
    {
        return Inertia::render('templates/AuthInner/Error/Cover404');
    }

    public function auth_404_alt()
    {
        return Inertia::render('templates/AuthInner/Error/Alt404');
    }

    public function auth_500()
    {
        return Inertia::render('templates/AuthInner/Error/Error500');
    }

    public function auth_pass_change_basic()
    {
        return Inertia::render('templates/AuthInner/PasswordCreate/BasicPasswCreate');
    }

    public function auth_pass_change_cover()
    {
        return Inertia::render('templates/AuthInner/PasswordCreate/CoverPasswCreate');
    }

    public function auth_offline()
    {
        return Inertia::render('templates/AuthInner/Error/Offlinepage');
    }

    // Landing

    public function landing()
    {
        return Inertia::render('templates/Landing/OnePage/index');
    }

    public function nft_landing()
    {
        return Inertia::render('templates/Landing/NFTLanding/index');
    }

    public function job_landing()
    {
        return Inertia::render('templates/Landing/JobLanding/index');
    }

    public function profile()
    {
        return Inertia::render('templates/Auth/user-profile');
    }
}
