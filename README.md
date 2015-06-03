# imaging-site-calendar

This web-page has been created for the Center for Translational Imaging and Precision Medicine at UC San Diego. It provides a calendar view for resource management used to schedule both patient and research scans. This page handels a single resource only.

Features include:

1. Single resource scheduling application with user login and multi-project reporting
2. Dashboard overview with latest news and plan for today
3. Calendar view just like Google and Outlook using fullcalendar (month, week and day view)
4. Schedule events based on project templates (15 minute intervals)
5. Project summary view with time already spend and time still availabe
6. Project report page with monthy sorted booking times and actual scans performed
7. Contact form sends email to maintainer

## Screenshots

Here is a screenshot of the application. "Order" is a calendar view. "Projects" a list of the current users projects with links to project reports. "Contact" allows users to send an email in order to request help.

![application screenshot](https://github.com/HaukeBartsch/imaging-site-calendar/blob/master/img/CalendarApp.png "Calendar App Screenshot")

After selecting an event the user can change the start/end time or assign the scan to a new project. Changing events is only possible if events are not in the past (given the current date).

![Change event screenshot](https://github.com/HaukeBartsch/imaging-site-calendar/blob/master/img/CalendarChangeEvent.jpg "Change event properties") 

The report page is availabel for each project and lists all booked times as well as actual scans performed on the scanner that overlap. This feature requires information available only on the scanner console. Currently a cron-job is used to "findscu" the scanner console to extract this information. The scans.php script is used to receive and locally store this information.

![report screenshot](https://github.com/HaukeBartsch/imaging-site-calendar/blob/master/img/Report.png "Calendar App Screenshot for Report Page")

