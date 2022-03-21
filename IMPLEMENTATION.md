# Write an implementation to handle the order stages in Symfony

Apparently there's something called Workflow in Symfony, which handles states and transitions,
and probably would have been a good way to deal with these things. But I've never used it before,
so to not spend hours/days to figure out how it works, I decided to go with an
"already know so quicker to implement" validator approach for now.

## Explain your approach to completing the tasks

As first step I decided to use API Platform for handling the API tasks, since I already know it fairly well.
I also added some additional useful libs I already know and use, to ease the implementation.
Next step was to identify what entities would be needed, which for the basic implementation I assumed would be:
- Client (customer) with id, name, email
- Product with id, name
- Type with id, name
- Stage with id, name
- Order with id and fkeys to the other entities

I just added the most basic fields needed for each one in this case, but most of the entities could use
some additional data, and I would normally also use the Timestampable and SoftDeletable traits.
For Client and Stage, I also thought it could be useful to "reverse link" to orders,
so it's easy for the company to see how many orders a client has, or how many orders are at a certain stage.

I generated the migrations off the back of the entities, and added a couple of fixtures to populate the db with.

I decided to use a sqlite db for testing, so they don't interfere with the data in the main db.

I didn't use factories/DTOs/mappers/sercives etc., as it felt unnecessary at this stage,
so saved it for a refactor if/when needed.

Re: stage transitions, see Q/A above. And re: unit testing, see further below.

Re: task for triggering actions, I went with a subscriber on the Order entity that checks if the stage has changed, and if so, creates and dispatches messages to the messenger system/queue to make it handling the tasks async, as they can be quite time consuming.

## Explain your assumptions and what other questions you would ask the user to ensure that you have all the information that you need to solve the task

- Assumptions made
The Document entity would be implemented when writing the stage actions.

Orders have to be created with the stage Created. Might be a Q to ask if it needs to be done differently.

Free trial could/would need some expire date, but implementation would depend on if contract orders need one too (Q to ask). If so, I would most likely put the field in the order table. If only for
trial, I would probably use a linked table (order_id as fkey in it) so order table doesn't end up with lots
of null entries.

- Additonal questions
Should all other fields in order besides stage be "locked" so they can't be changed?

Should a trial order be able to be converted to a contract order? And if so, what's the transition logic?

Can't a contract expire?

## Include unit tests, with any placeholder documentation that someone might need to continue working on your solution

I wrote small unit tests for each entity which sets, gets and asserts the data (dataIn === dataOut).

For functional testing, I just did a few basic tests to GET and POST clients. For the order endpoint,
I did the same plus a few trasitioning tests for both failed and successful commands.
Todo is to write more tests for the above ones, that tests with both good and bad data.
And obviously the same for the other endpoints I left out for this test/task.
Additional todo is to use a lib/trait that resets the db between each test, so it's always in a "clean"
state. This would require some of the tests to be rewritten, as at the moment they depend on the
additions/changes that some of the previous ones did.
Db gets cleared between test runs, but if wanna be truly certain, delete the file `./var/data.db`.

## Misc

There's a Swagger UI for the API at the `/api` path on the server (`http://localhost:8080/api` by default),
where one can view and play around with the endpoints.

Various commands:
In root folder:
- install deps: `composer i`
- run tests: `./bin/phpunit`

In docker folder:
- run migrations: `docker-compose exec php-fpm bin/console doctrine:migrations:migrate --all-or-nothing 1`
- load fixtures: `docker-compose exec php-fpm bin/console doctrine:fixtures:load`
- consume messages: `docker-compose exec php-fpm bin/console messenger:consume async -vv`
