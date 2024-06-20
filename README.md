## User Activity Tracker Plugin for WordPress

### Overview
This WordPress plugin tracks and records user activities performed within the admin area, including post edits and option changes. Activities are securely logged with details such as user ID, username, action performed, timestamp, and page URL reference.

### Features
- **Post Activity Tracking:** Records edits and deletions of posts, capturing post ID, title, type, and associated user details.
- **Option Changes Logging:** Tracks updates and deletions of WordPress options, logging old and new values, along with user information.
- **Secure Activity Storage:** Activities are stored as JSON files in a protected directory (`/wp-content/uploads/user-activity/`), ensuring data privacy and security with `.htaccess` restrictions.
- **Admin Dashboard Integration:** Provides an intuitive admin dashboard page to view and search logged activities, with pagination support.
- **Internationalization Support:** Fully translatable with localization support using the `user-activity-tracker` text domain.

### Installation
1. Download the plugin ZIP file or install directly from the WordPress Plugin Repository.
2. Activate the plugin through the Plugins menu in WordPress.
3. Navigate to the "User Activity Tracker" page in the WordPress admin to view and search logged activities.

### Contributing
Contributions are welcome! If you have suggestions, improvements, or find any issues, please [submit an issue](https://github.com/ithubdeveloper/user-activity-tracker/issues) or [create a pull request](https://github.com/ithubdeveloper/user-activity-tracker/pulls) on GitHub.

### Security
This plugin follows best practices for data handling and security. It sanitizes inputs, uses nonces for AJAX requests, and secures file operations to protect against vulnerabilities.

### License
Licensed under the [GNU General Public License v3.0](https://github.com/ithubdeveloper/user-activity-tracker/blob/main/LICENSE).

### Support
For support or inquiries, please [submit an issue](https://github.com/ithubdeveloper/user-activity-tracker/issues)

