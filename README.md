# User Activity Tracker Plugin for WordPress

**Overview**

This WordPress plugin tracks and records user activities performed within the admin area, including post edits and option changes. Activities are logged with details such as user ID, username, action performed, timestamp, and page URL reference.

**Features**

**Post Activity Tracking:** 
Records edits and deletions of posts, capturing post ID, title, type, and associated user details.
**Secure Activity Storage:** 
Stores activities as JSON files in a protected directory (/wp-content/uploads/user-activity/), ensuring data privacy and security with .htaccess restrictions.
**Admin Dashboard Integration:** 
Provides an admin dashboard page to view and search logged activities, with pagination support.
**Easy Setup:** 
Automatically creates necessary directories and sets up .htaccess protection on activation.

**Usage**

**Installation:** 
Download the plugin ZIP file and upload it to your WordPress site, or install directly from the WordPress Plugin Repository.
**Activation:** 
Upon activation, the plugin sets up the required directory structure (/wp-content/uploads/user-activity/) and protects it with .htaccess.
**Usage:** 
Navigate to the "User Activity Tracker" page in the WordPress admin area to view and search logged activities by user, date, or keyword.

**Contributing**
Contributions are welcome! If you have suggestions, improvements, or find any issues, please submit an issue or create a pull request on GitHub.

License
This plugin is licensed under the GNU General Public License v3.0.
