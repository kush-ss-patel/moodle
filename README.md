Similar IP Identification report

The goal of this Moodle-based online quiz/test is to capture the risk of academic dishonesty.

The report identifies students who used the same IP address to access Moodle modules. It sorts by modules and dates, among other things. Users' activities are saved in Moodle's database as log stores. This information comprises the action performed, the IP address, the origin of the IP address, the date, and the username.The same data is used in this report to discover IP addresses that have been logged with various users in the same activity.

Workings:

The report searches Moodle logs for fields containing the same IP address used by several students for a given module. For accurate results, it filters through the date and module.

Installation:

1st step

Unzip the folder that you downloaded.

2nd Step

Move the "examcheck" folder to the moodle/local folder on the server.

3rd step

Update the plugin by logging in as a moodle administrator. As soon as you open the dashboard, the addition should appear. After the update, go to any course->settings and look for a plugin called "examcheck."

If necessary, change the version number in version.php.

MIT License

Copyright (c) 2021 Kush Patel

Any person who obtains a copy of this software and associated documentation files (the "Software") is hereby granted free of charge permission to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit others to do so, if the following criteria are met:

All copies or substantial parts of the Software must carry the above copyright notice and this permission notice.

THE SOFTWARE IS PROVIDED "AS IS," WITHOUT ANY WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES, OR OTHER LIABILITY ARISING FROM, OUT OF, OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE, WHETHER IN CONTRACT, TORT, OR OTHERWISE.
