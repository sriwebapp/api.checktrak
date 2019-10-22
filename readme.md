# Check Trak

### User Group Access

-   System Admin: All Access + Telescope
-   Admin: All Modules, Actions and Branches
-   Head Office: Create, Transmit, Claim, Delete, Edit, Clear and Receive for All Branches, and Payee Module
-   Branch Office: Receive, Claim, Return check for specific Branch
-   Custom: Customized Access

### Modules

-   User Management
-   Company Management
-   Branch Management
-   Account Management
-   Payee Management
-   Check Management

### Todos Later

-   Group policy to block editing higher groups
-   Validation for two rows unique (payee, check)

### Reminders before deployment

-   Record head office first in branches
-   Consider Check receiving by transmittals
-   Modify receiving based on branch if transmitted

### todo 09-20-2019
- rename group to access -- ok
- groups under branches --ok
- multiple incharge per group --ok

- check user imports before deleting users --ok

### importing
- delete unneccessary columns
- in excel change format date('MM/DD/Y'), decimals
- remove empty lines
- from excel save as csv
- open notepad save as change to utf8


### Todo 09-30-2019
- import cleared checks -- ok
- create manual for importing --ok
- checks filtering --ok


-- from ui

# Checktrak

## Reminders

- Application Icon --ok
- Create toggle for logging ajax error
- Search for modules --ok
- remove all in pagination options --ok
- disable changes if no changes happen --ok
- redirect to 404 once model not found
- Username / instead of email --ok
- Send email after registered --ok
- Log activities in slack --ok
- Inactive Log In --ok
- clear store on logout --ok
- server side payee datatable/ update --ok
- add catch in all request --ok
- remove error in form show (dialogs) --ok
- month end/ year end inventory
- notification
- clarify cancellation

---

- cancel ---
- clear
- receive

---

- multiple incharge user group

## Seeding Data

- Group access
- Company
- Branch

-- todo minimize loading in startup --ok
-- access conditional changing --ok
-- create handler for 503
-- paginate transmittals --ok
-- throttle checks request
-- throttle unauthorized response
-- review cancel condition
-- clearing amount --ok
-- company change route address
