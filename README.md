# Nadlanicrm

Nadanicrm - feature list

Complete Field Customisations


an administrator can add custom field to any type of data - properties, leads, accounts, property requests and any other
the crm is intended for realtors and property related business, but can easily be extended to support additional datasets using custom fields
the crm supports api calls to  populate any type of field including users, properties, leads, accounts, locations etc
the api can also be used to “pull” from the crm data and display it in any other platform
the crm is very scalable - and uses MYSQL as the storage engine, supporting unlimited storage (usually databases are  limited by the constraints of the os used )
check this table directly taken from the following link as a reference to database comparisons and constraints.

Query
Indexes
Database
P,C1
C,P2
Oracle 10g XE
MySQL 5.0.49a (MyISAM)
SQL Server 2008
Aggregation
No
No
1.329s
0.703s4
6.920s
Yes
No
1.329s
2.219s
5.983s
No
Yes
0.219s
0.406s
0.436s
Yes
Yes
0.230s
0.406s
0.416s
Join
No
No
0.729s
Failed5
6.656s
Yes
No
0.719s
2.750s
6.796s
No
Yes
0.094s
0.704s
0.670s3
Yes
Yes
0.094s
0.813s
0.423s


as you can see the simple and complex searches are also extremely fast on mysql.


in the crm - any administrator / company manager /company  owner - can assign unlimited users / agents for his company.
any agent can hold unlimited amount of properties, clients, accounts and leads and any other type of data the administrator defined before hand.
the best approach in terms of privacy for realty companies is to setup a single group / team for public properties that belong to the crm system - eg: mls , then assign every user to his company company and create a team for each new comapny - eg : realty agents and define access rights to the public property group - the mls group.
-------------------this way all properties that are added to the crm are public, in terms of stage,assigned agent and any other field but are still unavailable to agents that arent asigned to that property or that belong to a different team / company.
this allows higher conversion rates - especially when there are several companies in the same crm- so all companies can reach the property when they fill a property request for a property seeker / client of their’s, and see only the fields that the administrator defined in the access eg : the assigned agent contact detailsor any other field.
 roles : and admistrator can create roles and control exactly what users in that role can see, edit, delete and search. 
users can be assigned to more then one role - in which case they will recieve the entire permission as defined in all roles. 
if needed - permissions can be restriced using teams too - in this way it is only needed to setup the team permissions in the admin panel and all users in that team have join permission - making delegation setup much faster.
for example 3 companies - remax north, city idle, and remax south joined the online crm. each company has their administrator - and he and only he can assign existing company properties to his agents. using the teams it is possible and fairly simple to create a rule using a common team - for this example well call the team “remax london” (because both remax agencies operate in london ) allowing all remax admins to see only properties owned by them - so that the admin from remax north can ask the admin in remax south to continues with a property / lead.
in the same way if the crm owner decides - it is fairly easy to create one global team for all new leads - and setup a rule that all leads will exist in that team and in the company teams. 

property requests
 when a lead who is a home seeker or a home owner (can be defined by a custom field or team rules) is looking for a property, the life cycle begins by agents filling in a property request. in the property request they will fill up all the data that was collected after a meeting with that lead, eg: desired home size in sq meters, desired neighbourhood, desired rooms and floor. immediately after publishing the property request - all matching properties in the entire crm that match the rules entered will show up under. *it can again easily changed to only properties in the company the agent works in, but for this user case we defined that all vacant properties the agents failed to find a lead to in ALL Registered companies are held in the mls team and so public for query to all agents throughout the system. 
when the agent who filled up the property request wants to see a certain property that might match his clients needs- if the property is owned by another company - then that agent will be redirected to the company contact, if the property is being held by another agent but no deal is yet closed then that agents’ contact details will show up.


all properties should be defined as “undeletable”, since no matter where they are, or what their current status is-  they will always remain more or less the same. (even if a building is torn down - the address is the same - think of it as a constant, how often do new properties get built?

the same rule should be applied to leads since home seekers and home owners are also a constants - when a property gets sold the owner and seller should remain in the system since sooner or later they will have to buy and sell the same property.


CRM additional extendability :
the crm support several voip (voice over ip) ready solutions and can easily be integrated with 3rd party software like wordpress, php and asap based solutions using the CRM’s native api or the database itself wich can be esily integreted to other systems- example use case : datatables, or this custom plugin for worpress : https://wpdatatables.com/ which supports external database connections
 an android app can be used as a webview or a more customised solution to allow pulling of specific data - eg :emails / event /tasks


Portals

Each company working under the crm has the ability to list any data into one of the main public feeds also called portals.
each company also has the abilty to open new custom portals
Each portal can be fine tuned as to who can edit the info and view everyhing in that portal.
For example - remax north wants to showcase all the openhouses property type for potential clients they are working with. the portal has to allow potential clients to view all the custom type properties, and also allow the agents to edit the property vacancy - since after a client views a certian property he might enlist himself as a buyer and after the agent goes through with validating the deal the specific property is no longer available to “resold” and so needs to be hidden from public view.
this is easily achieved in the followin manner : they (the property company - in this case “remax north” opens up a public portal - 

in the next step they’ll have to select the entity type to be displayed in that portal and all the different roles and the matching permissions for each role.






in this screen there are 3 main fields that are crucial - 
the role field through which the ability to control visibility is fine grained, and the “tablist” and
quick create list on the bottom through which they can control which entities are displayed in that portal. 
note - no matter what entities are chosen in this screen - properties / contacts / leads or any other - the permissions granted at the portal role creation always do the actual control on entity visibility and edit permission. in other word if “remax north” chose realtors and leads to be a part of that portal’s role list, then the roles chosen will be able to login to that portal but will have only the permissions granted to them in their initial creation.
so if for example “remax north” chose leads as a part on roles that can access that portal but the leads role has no read/write privileges to the property entity the any lead user that signs into the portal will not be able to do anything!







so in each portal creation we get the ption to assign visibilty to all custom entities in the crm 
there are 3 main option per entity in each of the actions specified in the top menu 
the action are : 


Access
Create
Read
Edit
Delete
Stream
access means the ability to access that entity type
create is as the name implies the ability to create new entities in that entity type
read is the ability to read any entity of that entity type eg: tasks, properties, leads etc
edit is the ability to edit an existing entity
delete is the ability to delete any entity in that entity type
stream is the ability to add any action to the public stream
under each of these there are 4 main options- yes, no, all, own
           yes is basically the general permission and is displayed in the access tab -        allowing to either allow or deny access to a specific entity type or deny
no is as the name implies - deny any access too that entity type
all means that all entities under a  given type will be available for the corresponding action regardless of the owner of that entity
own means only the owner / creator / assigned user will have the ability to make changes.


for now lets focus on the properties custom entity type - 

using the permissions the portal creator will be able the fine grain the permissions or properties in the new portal role even further - i.e : he might want the role currently being created (leads_portal) to be able to access properties, but no “create” permissions(since leads arent realty agents) , read all permissions, or read own (if read all - all the properties in the crm will be made available to that role to read, 
if own - the only entities created by the portal creator will be made available, 
edit permissions can be set to all (ill show you how to fine grain that further to suit the company needs,) delete should be set to no - as we said - no properties added to the system should ever be deleted) , and stream is as you wish - if the compny owner wants to allow everyone with access to that portal to be able to see the operations made by a lead publicly he’ll set it to “all”, or he can set it to own - meaning the changes will be made publicly visible only to the owners / assigned users to a specific entity. 


