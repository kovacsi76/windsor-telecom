# Symfony dev task

Thank you for taking the time to complete this test. Good luck!

## Background

Windsor Telecom wish to sell products to their customers. They will do this via an order system. There are two types of orders; free trial and contract. Each order will go
 through 4 core stages and there will be additional stages for each type. It is likely that other order types will be added in the future. The following stages are required right now.

- Created
- Approved
- Signed (contract only)
- Delivered
- Completed
- Expired (free trial only)

When a stage has been completed the following actions should happen:

- Created - email the customer with their requested order
- Approved - email the customer with a link to signing the contract (contract only)
- Signed - store the PDF on our server (this will be retrieve via an API call using the order ID)
- Delivered - email the sales team to follow up with the customer
- Completed - no action required
- Expired - email the customer to alert them the trial has expired, and to email the sales team to follow up with the customer (trial only)

We have included these actions and their purpose for illustration purposes only, you are not expected to implement each action, however you should showcase how the required functionality may be structured and triggered.

## Tasks

Using the Symfony project skeleton we have given, you are required to complete the following tasks:

- Create entities for your solution (feel free to add whatever fields you would like)
- Ability to transition between stages via API endpoint
- Showcase your code structure for how the actions will be triggered
- Any required database migrations
- Some data fixtures to help you while developing

Please feel free to add any external packages to help you implement this project.

## The minimum requirements for the application are:

- Write an implementation to handle the order stages in Symfony
- Explain your approach to completing the tasks
- Explain your assumptions and what other questions you would ask the user to ensure that you have all the information that you need to solve the task
- Include unit tests, with any placeholder documentation that someone might need to continue working on your solution
- Put your code online (e.g. in GitLab/GitHub) and send us the link.

## Nice to see

Add something to the solution that you believe would enhance the system
 
We are not expecting a full solution and are just looking for an insight on how you might approach a specific task.

## Up and running with Docker

Before using docker please ensure you have run the following commands:
`cp docker/.env.dist docker/.env`
`cp .env .env.local`

If you would like to change any ports you can do so in `docker/.env`. These will need to be changed if you are already using port `8080` and/or `4306` on your machine 

To get the project up and running for the first time run `make build`.
After this you can run `make up` to spin up the containers and `make bash` to run a bash command inside the `php-fpm` container.
If you would like to rebuild the project at anytime you can run `make build` again. 

By default you can access the project at `http://localhost:8080`

Please note when setting up the project, if you encounter permission issues, ensure that your local user id and group id has been set correctly in `/docker/.env`
To find out your user id run `id -u` and for group id run `id -g` in you terminal.   

Happy coding!
