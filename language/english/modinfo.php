<?php

// Module Info

// The name of this module
define('_MI_ADS_NAME', 'Classifieds');

define('_MI_ADS_MENUADD', 'Add an Advertisement');

// A brief description of this module
define('_MI_ADS_DESC', 'Classified Ad Module');

// Names of blocks for this module (Not all module has blocks)
define('_MI_ADS_BNAME', 'Classified Ad Block');
define('_MI_ADS_BNAME_DESC', 'Classified Ad Listing Block');

// Names of admin menu items
define('_MI_ADS_ADMENU1', 'Administration');
define('_MI_ADS_ADMENU2', 'Category Maintenance');
define('_MI_ADS_ADMENU3', 'Permissions');
define('_MI_ADS_ADMENU4', 'Preferences');
define('_MI_ADS_ADMENU5', 'Blocks Admin');
define('_MI_ADS_ADMENU6', 'Go to Module');
define('_MI_ADS_CONFSAVE', 'Configuration saved');
define('_MI_ADS_CANPOST', 'Anonymous user can post Listings :');
define('_MI_ADS_PERPAGE', 'Listings per page :');
define('_MI_ADS_RES_PERPAGE', 'Resumes per page :');
define('_MI_ADS_MONEY', 'Currency symbol :');
define('_MI_ADS_KOIVI', 'Use Koivi Editor :');
define('_MI_ADS_NUMNEW', 'Number of new Listings :');
define('_MI_ADS_MODERAT', 'Moderate Listings :');
define('_MI_ADS_DAYS', 'Listing Duration :');
define('_MI_ADS_MAXIIMGS', 'Maximum Photo Size :');
define('_MI_ADS_MAXWIDE', 'Maximum Photo Width :');
define('_MI_ADS_MAXHIGH', 'Maximum Photo Height :');
define('_MI_ADS_TIMEANN', 'Listing duration :');
define('_MI_ADS_INBYTES', 'in bytes');
define('_MI_ADS_INPIXEL', 'in pixels');
define('_MI_ADS_INDAYS', 'in days');
define('_MI_ADS_MUSTLOGIN', 'Allow anonymous users to reply to a classified ad.');
define('_MI_ADS_THRUMAIL', 'using the e-mail form (recommended is No, because of spam.)');
define('_MI_ADS_TYPEBLOC', 'Type of Block :');
define('_MI_ADS_JOBRAND', 'Random Listing');
define('_MI_ADS_LASTTEN', 'Last 10 Listings');
define('_MI_ADS_NEWTIME', 'New Listings from :');
define('_MI_ADS_DISPLPRICE', 'Display price :');
define('_MI_ADS_DISPLPRICE2', 'Display price :');
define('_MI_ADS_INTHISCAT', 'in this category');
define('_MI_ADS_DISPLSUBCAT', 'Display subcategories :');
define('_MI_ADS_ONHOME', 'on the Front Page of Module');
define('_MI_ADS_NBDISPLSUBCAT', 'Number of subcategories to show :');

define('_MI_ADS_IF', 'if');
define('_MI_ADS_ISAT', 'is at');
define('_MI_ADS_VIEWNEWCLASS', 'Show new Listings :');
define('_MI_ADS_ORDREALPHA', 'Sort alphabetically');
define('_MI_ADS_ORDREPERSO', 'Personalised Order');
define('_MI_ADS_ORDRECLASS', 'Category Order :');

////////////////////////////////////////////////////////
//added below for version 2.0
////////////////////////////////////////////////////////

    define('_MI_ADS_GPERM_G_ADD', 'Can add');
    define('_MI_ADS_CAT2GROUPDESC', 'Check categories which you allow to access');
    define('_MI_ADS_GROUPPERMDESC', 'Select group(s) allowed to submit listings.');
    define('MI_ADS_GROUPPERM', 'Submit Permissions');
    define('_MI_ADS_SUBMITFORM', 'Ads Submit Permissions');
    define('_MI_ADS_SUBMITFORM_DESC', 'Select, who can submit Ads');
    define('_MI_ADS_VIEWFORM', 'View Ads Permissions');
    define('_MI_ADS_VIEWFORM_DESC', 'Select, who can view Ads');
    define('_MI_ADS_VIEW_RESUMEFORM_DESC', 'Select, who can view resumes');
    define('_MI_ADS_SUPPORT', 'Support this software');
    define('_MI_ADS_OP', 'Read my opinion');
    define('_MI_ADS_PREMIUM', 'Ads Premium');
    define('_MI_ADS_PREMIUM_DESC', 'Who can select days listing will last');

// Notification event descriptions and mail templates

define('_MI_ADS_CATEGORY_NOTIFY', 'Category');
define('_MI_ADS_CATEGORY_NOTIFYDSC', 'Notification options that apply to the current category.');
define('_MI_ADS_NOTIFY', 'Listing');
define('_MI_ADS_NOTIFYDSC', 'Notification options that apply to the current listing.');
define('_MI_ADS_GLOBAL_NOTIFY', 'Whole Module ');
define('_MI_ADS_GLOBAL_NOTIFYDSC', 'Global advert notification options.');

//event

define('_MI_ADS_NEWPOST_NOTIFY', 'New Listing');
define('_MI_ADS_NEWPOST_NOTIFYCAP', 'Notify me of new listings in the current category.');
define('_MI_ADS_NEWPOST_NOTIFYDSC', 'Receive notification when a new listing is posted to the current category.');
define('_MI_ADS_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: auto-notify : New listing in category');
define('_MI_ADS_VALIDATE_NEWPOST_NOTIFY', 'New Listing');
define('_MI_ADS_VALIDATE_NEWPOST_NOTIFYCAP', 'Notify me of new listings in the current category.');
define('_MI_ADS_VALIDATE_NEWPOST_NOTIFYDSC', 'Receive notification when a new listing is posted to the current category.');
define('_MI_ADS_VALIDATE_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: auto-notify : New listing in category');
define('_MI_ADS_UPDATE_NEWPOST_NOTIFY', 'Listing Updated');
define('_MI_ADS_UPDATE_NEWPOST_NOTIFYCAP', 'Notify me of updated listings in the current category.');
define('_MI_ADS_UPDATE_NEWPOST_NOTIFYDSC', 'Receive notification when an listing is updated in the current category.');
define('_MI_ADS_UPDATE_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: auto-notify : New listing in category');
define('_MI_ADS_DELETE_NEWPOST_NOTIFY', 'Listing Deleted');
define('_MI_ADS_DELETE_NEWPOST_NOTIFYCAP', 'Notify me of new listings in the current category.');
define('_MI_ADS_DELETE_NEWPOST_NOTIFYDSC', 'Receive notification when an listing is deleted from the current category.');
define('_MI_ADS_DELETE_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: auto-notify : New listing in category');
define('_MI_ADS_GLOBAL_NEWPOST_NOTIFY', 'New Listing');
define('_MI_ADS_GLOBAL_NEWPOST_NOTIFYCAP', 'Notify me of new listings in all categories.');
define('_MI_ADS_GLOBAL_NEWPOST_NOTIFYDSC', 'Receive notification when a new listing is posted to all categories.');
define('_MI_ADS_GLOBAL_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: auto-notify : New listing in category');
define('_MI_ADS_GLOBAL_VALIDATE_NEWPOST_NOTIFY', 'New Listing');
define('_MI_ADS_GLOBAL_VALIDATE_NEWPOST_NOTIFYCAP', 'Notify me of new listings in all categories.');
define('_MI_ADS_GLOBAL_VALIDATE_NEWPOST_NOTIFYDSC', 'Receive notification when a new listing is posted to all categories.');
define('_MI_ADS_GLOBAL_VALIDATE_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: auto-notify : New listing in category');
define('_MI_ADS_GLOBAL_UPDATE_NEWPOST_NOTIFY', 'Listing Updated');
define('_MI_ADS_GLOBAL_UPDATE_NEWPOST_NOTIFYCAP', 'Notify me of updated listings in all categories.');
define('_MI_ADS_GLOBAL_UPDATE_NEWPOST_NOTIFYDSC', 'Receive notification when an listing is updated in all categories.');
define('_MI_ADS_GLOBAL_UPDATE_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: auto-notify : Listing updated in categories');
define('_MI_ADS_GLOBAL_DELETE_NEWPOST_NOTIFY', 'Listing Deleted');
define('_MI_ADS_GLOBAL_DELETE_NEWPOST_NOTIFYCAP', 'Notify me of deleted listings in all categories.');
define('_MI_ADS_GLOBAL_DELETE_NEWPOST_NOTIFYDSC', 'Receive notification when an listing is deleted in all categories.');
define('_MI_ADS_GLOBAL_DELETE_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: auto-notify : Listing deleted in categories');

// Added for ratings

define('_MI_ADS_RATE_USER', 'Allow Users to rate sellers');
define('_MI_ADS_RATE_ITEM', 'Allow Users to rate items');
