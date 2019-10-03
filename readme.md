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
- groups under branches
- multiple incharge per group

- check user imports before deleting users

### importing
- delete unneccessary columns
- in excel change format date('MM/DD/Y'), decimals
- remove empty lines
- from excel save as csv
- open notepad save as change to utf8


### Todo 09-30-2019
- import cleared checks -- ok
- create manual for importing --
- checks filtering


-- from ui

# Checktrak

## Reminders

- Application Icon
- Create toggle for logging ajax error
- Search for modules
- remove all in pagination options ---
- disable changes if no changes happen ---
- redirect to 404 once model not found ---
- Username / instead of email
- Send email after registered ---
- Log activities in slack ---
- Inactive Log In
- clear store on logout ---
- server side payee datatable/ update
- add catch in all request
- remove error in form show (dialogs)
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

-- todo minimize loading in startup
-- access conditional changing
-- create handler for 503
