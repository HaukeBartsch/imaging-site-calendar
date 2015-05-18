# imaging-site-calendar

This web-page has been created for the Center for Translational Imaging and Precision Medicine at UC San Diego. It provides a calendar view for resource management used to schedule both patient and research scans. This page handels a single resource only.

## Screenshots

Here is a screenshot of the whole page. "Order" is a calendar page. "Projects" is a list of the current users projects with links to project reports. "Contact" allows users to send an email in order to request help.

![application screenshot](https://github.com/HaukeBartsch/imaging-site-calendar/blob/master/img/CalendarApp.png "Calendar App Screenshot")

The report page is availabel for each project and lists all booked times as well as actual scans performed on the scanner that overlap. This feature requires information available only on the scanner console. Currently a cron-job is used to "findscu" the scanner console to extract this information. The scans.php script is used to receive and locally store this information.

![report screenshot](https://github.com/HaukeBartsch/imaging-site-calendar/blob/master/img/Report.png "Calendar App Screenshot for Report Page")

