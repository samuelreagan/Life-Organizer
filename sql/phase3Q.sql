-- ============ Convert your E/R to relations 

DROP VIEW IF EXISTS sams_goals;
DROP TABLE IF EXISTS habit_log;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS notes;
DROP TABLE IF EXISTS friends;
DROP TABLE IF EXISTS challenges; 
DROP TABLE IF EXISTS earned;
DROP TABLE IF EXISTS habits;
DROP TABLE IF EXISTS goals;
DROP TABLE IF EXISTS users;

-- ============ CREATE TABLE's 
CREATE TABLE users(
  id SERIAL, 
  name varchar(255),
  email varchar(255) UNIQUE,
  password varchar(255),
  level int,
  xp int,
  avatar_url text,
  PRIMARY KEY(id),
  CHECK ((level >= 1 AND level <= 10) AND xp >= 0)
);

CREATE TABLE goals(
  id SERIAL,
  user_id int NOT NULL,
  name varchar(50),
  body text,
  is_complete boolean,
  deadline date default NULL,
  date_created date default CURRENT_DATE,
  date_completed date default NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE tasks(
  id SERIAL,
  user_id int NOT NULL,
  goal_id int default NULL,
  body text,
  priority int NOT NULL CHECK (priority >= 0),
  is_complete boolean,
  deadline date default NULL,
  date_created date default CURRENT_DATE,
  date_completed date default NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(goal_id) REFERENCES goals(id) ON DELETE CASCADE
);

CREATE TABLE habits(
  id SERIAL,
  name varchar(255),
  user_id int,
  PRIMARY KEY(id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE habit_log(
  id SERIAL,
  habit_id int,
  user_id int,
  date_completed date default CURRENT_DATE,
  PRIMARY KEY(id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(habit_id) REFERENCES habits(id) ON DELETE CASCADE
);

CREATE TABLE notes(
  id SERIAL,
  user_id int,
  title varchar(255) NOT NULL,
  body text,
  date_created date default CURRENT_DATE,
  PRIMARY KEY(id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE friends(
  id SERIAL,
  friend_one int,
  friend_two int,
  PRIMARY KEY(id),
  FOREIGN KEY(friend_one) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(friend_two) REFERENCES users(id) ON DELETE CASCADE,
  CHECK(friend_one <> friend_two)
);

CREATE TABLE challenges(
  id SERIAL,
  name varchar(50),
  body varchar(255),
  badge_url text,
  PRIMARY KEY(id)
);

CREATE TABLE earned(
  id SERIAL,
  user_id int,
  challenge_id int,
  PRIMARY KEY(id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============ INSERT DATA
INSERT INTO users(name, email, password, level, xp, avatar_url) VALUES('Sam', 'sam@gmail.com', 'coolPassword123', 1, 56, '../images/avatar12.png');
INSERT INTO users(name, email, password, level, xp, avatar_url) VALUES('Jordan Peterson', 'jordan@gmail.com', 'testing123', 2, 100, '../images/avatar23.png');
INSERT INTO users(name, email, password, level, xp, avatar_url) VALUES('Jocko Willink', 'jocko@gmail.com', 'workout123', 3, 300, '../images/avatar34.png');
INSERT INTO users(name, email, password, level, xp, avatar_url) VALUES('Joe Rogan', 'joe@gmail.com', 'mma123!', 1, 75, '../images/avatar45.png');

INSERT INTO goals(user_id, name, body, is_complete, deadline, date_completed) VALUES(3, 'Write a book', 'I want to write a book to help people become more disciplined.', true, '2018-01-01', '2017-12-25');
INSERT INTO goals(user_id, name, body, is_complete, deadline, date_completed) VALUES(4, 'Start a business', 'I want to start a business that sells clothing.', false, NULL, NULL);
INSERT INTO goals(user_id, name, body, is_complete, deadline, date_completed) VALUES(2, 'President', 'I want to become the president of Canada.', false, NULL, NULL);
INSERT INTO goals(user_id, name, body, is_complete, deadline, date_completed) VALUES(1, 'Graduate', 'I want to graduate college debt free.', false, '2019-05-12', NULL);

INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (3, 1, 'Get a publisher', '1', true, '2017-01-01', '2016-12-01');
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (3, 1, 'Write rough draft', '2', true, '2017-08-01', '2017-07-21');
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (3, 1, 'Edit rough draft', '3', true, '2017-09-01', '2017-09-01');
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (3, 1, 'Publish', '4', true, '2018-01-01', '2017-12-25');
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (1, NULL, 'Do Database Homework', '1', false, '2018-10-18', NULL);
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (1, NULL, 'Write Software Engineering Report', '2', false, '2018-10-18', NULL);
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (1, 4, 'Pass all classes in the Fall 2018 semester', '1', false, '2018-12-14', NULL);
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (1, 4, 'Pass all classes in the Spring 2019 semester', '2', false, '2018-05-12', NULL);
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (4, 2, 'Make Plan', '1', true, NULL, '2018-09-09');
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (4, 2, 'Start LLC', '2', false, NULL, NULL);
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (4, 2, 'Create Website', '3', false, NULL, NULL);
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (4, 2, 'Get Product', '4', false, NULL, NULL);
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (4, 2, 'Open for business', '5', false, NULL, NULL);
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (2, 3, 'Fill out intent to run form', '1', false, NULL, NULL);
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (2, 3, 'Get funding', '2', false, NULL, NULL);
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (2, 3, 'Create ads', '3', false, NULL, NULL);
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (2, 3, 'Debate', '4', false, NULL, NULL);
INSERT INTO tasks(user_id, goal_id, body, priority, is_complete, deadline, date_completed) VALUES (2, 3, 'Win', '5', false, NULL, NULL);

INSERT INTO habits(name, user_id) values('Wake up at 4:30am', 3);
INSERT INTO habits(name, user_id) values('Self Reflect Daily', 2);
INSERT INTO habits(name, user_id) values('Go to the gym', 1);
INSERT INTO habits(name, user_id) values('Yoga', 4);
INSERT INTO habits(name, user_id) values('Read 15 minutes', 1);
INSERT INTO habits(name, user_id) values('Write an hour a day', 2);

INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(3, 1, '2018-10-01');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(3, 1, '2018-10-02');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(3, 1, '2018-10-03');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(3, 1, '2018-10-04');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(3, 1, '2018-10-05');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(3, 1, '2018-10-06');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(3, 1, '2018-10-07');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(2, 2, '2018-10-01');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(2, 2, '2018-10-04');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(2, 2, '2018-10-07');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(1, 1, '2018-10-01');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(1, 1, '2018-10-02');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(1, 1, '2018-10-03');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(1, 1, '2018-10-04');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(1, 1, '2018-10-05');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(4, 4, '2018-10-04');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(4, 4, '2018-10-05');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(5, 1, '2018-10-06');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(6, 2, '2018-10-05');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(6, 2, '2018-10-06');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(6, 2, '2018-10-15');
INSERT INTO habit_log(habit_id, user_id, date_completed) VALUES(6, 2, '2018-10-16');

insert into friends(friend_one, friend_two) values (1,4);
insert into friends(friend_one, friend_two) values (2,4);
insert into friends(friend_one, friend_two) values (2,1);

INSERT INTO notes(title, user_id, body, date_created) VALUES('Book List',1,'Farenheit 451, 1984, Brave New World', '2018-05-05');
INSERT INTO notes(title, user_id, body, date_created) VALUES('Workout Days',2, 'Monday: Chest, Tuesday: Legs, Wednesday: Shoulders, Thursday: Arms, Friday: Cardio', '2018-06-05');
INSERT INTO notes(title, user_id, body, date_created) VALUES('Bruce Lee Quote',3, 'If you spend too much time thinking about a thing, you will never get it done.', '2018-07-15');
INSERT INTO notes(title, user_id, body, date_created) VALUES('Podcast to look up',4, 'Tim Ferris Podcast', '2018-09-25');

INSERT INTO challenges(name, body, badge_url) VALUES('Newbie', 'Sign Up', '../img/badges/level1.png');
INSERT INTO challenges(name, body, badge_url) VALUES('Novice', 'Gain 100 XP', '../img/badges/level2.png');
INSERT INTO challenges(name, body, badge_url) VALUES('Rookie', 'Gain 250 XP', '../img/badges/level3.png');
INSERT INTO challenges(name, body, badge_url) VALUES('Intermediate', 'Gain 400 XP', '../img/badges/level4.png');
INSERT INTO challenges(name, body, badge_url) VALUES('Skilled', 'Gain 600 XP', '../img/badges/level5.png');
INSERT INTO challenges(name, body, badge_url) VALUES('Proficient', 'Gain 900 XP', '../img/badges/level6.png');
INSERT INTO challenges(name, body, badge_url) VALUES('Experienced', 'Gain 1400 XP', '../img/badges/level7.png');
INSERT INTO challenges(name, body, badge_url) VALUES('Advanced', 'Gain 2000 XP', '../img/badges/level8.png');
INSERT INTO challenges(name, body, badge_url) VALUES('Expert', 'Gain 3000 XP', '../img/badges/level9.png');
INSERT INTO challenges(name, body, badge_url) VALUES('Master', 'Gain 4500 XP', '../img/badges/level10.png');

INSERT INTO earned(user_id, challenge_id) VALUES(1,1);
INSERT INTO earned(user_id, challenge_id) VALUES(2,1);
INSERT INTO earned(user_id, challenge_id) VALUES(3,1);
INSERT INTO earned(user_id, challenge_id) VALUES(4,1);
INSERT INTO earned(user_id, challenge_id) VALUES(2,2);
INSERT INTO earned(user_id, challenge_id) VALUES(3,2);
INSERT INTO earned(user_id, challenge_id) VALUES(3,3);
INSERT INTO earned(user_id, challenge_id) VALUES(1,6);

-- ============ Queries 

 
-- ============ 8 simple queries (similar to the examples below)
--              operators includes (and,or,not) 
--              patterns

   -- 1. Find all the user's
   SELECT * FROM  users;

   -- 2. Find all the challenges that begin with Level
   SELECT * FROM challenges WHERE name LIKE 'Level%';

   -- 3. Find all users above level 1
   SELECT name FROM users WHERE level > 1;

   -- 4. Find completed goals by Jocko
   SELECT name FROM goals WHERE user_id = 3 AND is_complete = TRUE;

   -- 5. Find all tasks completed in 2017
   SELECT body, user_id FROM tasks where is_complete = TRUE AND date_completed > '2016-12-31' AND date_completed < '2018-01-01';

   -- 6. Find all friend id's of Joe
   SELECT * FROM friends WHERE friend_one = 4 OR friend_two = 4;

   -- 7. Find all tasks that are part of a goal
   SELECT body, user_id, goal_id from TASKS where goal_id IS NOT NULL;

   -- 8. Find the habits logged for all users iin the first week of October
   SELECT * FROM habit_log WHERE date_completed >= '2018-10-01' AND date_completed <= '2018-10-07' ORDER BY user_id;


 -- ============= 6 Multirelation queries (two or more relations  
 --                                        in the FROM-clause)
 -- (similar to the examples below) 

   -- 9. Find Sam's Goals
   SELECT goals.name, goals.body FROM users, goals WHERE users.id = goals.user_id AND  users.name = 'Sam';

   -- 10. Find the notes created by Jordan
   SELECT notes.title, notes.body FROM notes, users WHERE users.id = notes.user_id AND users.id = 2;

   -- using operators and or not

   -- 11. Find the names of the tasks Joe hasn't completed
   SELECT users.name, tasks.body FROM users, tasks WHERE users.id = tasks.user_id AND tasks.is_complete = FALSE AND users.id = 4;

   -- 12. Find the names of the users and the challenges they've completed
   SELECT users.name, challenges.name FROM users, challenges, earned WHERE users.id = earned.user_id AND challenges.id = earned.challenge_id;

   -- 13. Finds the tasks that are a part of Jordan's goal to become president
   SELECT tasks.body FROM tasks, goals WHERE goals.id = tasks.goal_id AND tasks.goal_id IS NOT NULL AND goals.user_id = 2 AND goals.id = 3;

   -- 14. Find the habits that Jordan and Jocko are keeping track of
   SELECT users.name, habits.name FROM habits, users WHERE users.id = habits.user_id AND (users.id = 2 OR users.id = 3);

 -- ============= 6 Subqueries like below

   --15. FROM (subquery): Find users that have logged habits in October 
   SELECT DISTINCT users.name FROM users, 
    (SELECT habit_log.user_id 
     FROM habit_log
     WHERE date_completed > '2018-09-30' AND date_completed < '2018-11-01') november_habits  
   WHERE users.id = november_habits.user_id;

   --16. WHERE IN: Select users that have completed challenges
   SELECT name FROM users WHERE id IN(SELECT id FROM earned);
    
   --17. EXISTS: Select Users that haven't completed tasks (e.g. unique, all)
   SELECT users.name FROM users WHERE EXISTS(SELECT * FROM tasks WHERE is_complete = FALSE and date_completed IS NULL AND users.id = tasks.user_id);
   
   --18. ANY: Find users with incomplete goals
   SELECT users.name FROM users WHERE users.id = ANY(SELECT goals.user_id FROM goals WHERE is_complete = FALSE);
   
   --19. ALL: Find the user with the highest level
   SELECT u1.name, u1.level FROM users u1 WHERE u1.level > ALL(SELECT u2.level FROM users u2 WHERE u2.id <> u1.id);

   --20. Find the users that haven't taken notes
  SELECT users.name FROM users WHERE users.id NOT IN (SELECT notes.user_id FROM notes GROUP BY notes.user_id HAVING count(notes.title) <> 0);
--=============================================================

 -- ===============  5 SQL-statements using union, intersect, difference (except) 

   -- 21. Find the challenges that haven't been completed by any users.
   (SELECT challenges.name FROM challenges) 
    EXCEPT
   (SELECT challenges.name FROM challenges WHERE challenges.id IN(SELECT challenge_id FROM earned));

   -- 22. Fnd the users without friends
   (SELECT users.name FROM users WHERE users.id NOT IN(SELECT friend_one FROM friends WHERE users.id = friends.friend_one))
    INTERSECT 
   (SELECT users.name FROM users WHERE users.id NOT IN(SELECT friend_two FROM friends WHERE users.id = friends.friend_two));

   -- 23. Find the habits that haven't been logged today
   (SELECT habits.name FROM habits)
    EXCEPT
   (SELECT habits.name FROM habits WHERE habits.id IN(SELECT habit_log.habit_id FROM habit_log WHERE date_completed = CURRENT_DATE));

   -- 24. Find the goals that aren't complete and don't have a deadline
   (SELECT goals.name, goals.body FROM goals WHERE is_complete = FALSE)
    INTERSECT
   (SELECT goals.name, goals.body FROM goals WHERE deadline IS NOT NULL);

   -- 25. Find Sam's and Jordan's Habits
   (SELECT habits.name AS habit FROM habits WHERE habits.user_id = 1)
    UNION 
   (SELECT habits.name AS habit FROM habits WHERE habits.user_id = 2);




  -- ===============5 SQL-statements using Join ==================================
   -- using CROSS JOIN, NATURAL JOIN, THETA JOIN (INNER JOIN)
   -- 26. Find the habits for each user
   SELECT habits.name AS habit, users.name FROM habits JOIN users ON users.id = habits.user_id;

   -- 27. Find the goals for each user
   SELECT goals.name as goal, users.name FROM goals JOIN users ON users.id = goals.user_id;

   -- 28. Find the user's habit log
   SELECT users.name, habit_log.habit_id, habit_log.date_completed  FROM users NATURAL JOIN habit_log;

   -- 29. Find the friend pairs
   SELECT f1.friend_one, f2.friend_two FROM friends f1 NATURAL JOIN friends f2;

   -- 30. Find the challenges for each user 
   SELECT users.name, challenges.id FROM users CROSS JOIN challenges; 




 -- =============================== OUTER JOIN =============================
   -- using LEFT, RIGHT, FULL OUTER JOIN

   -- 31. Find the notes by all users
   SELECT notes.title as notes, users.name FROM notes LEFT OUTER JOIN users ON notes.id = users.id;

   -- 32. Find the challenges completed by users
   SELECT DISTINCT challenges.name as challenges FROM challenges RIGHT OUTER JOIN earned ON challenges.id = earned.challenge_id;

   -- 33. Find the tasks for every goal
   SELECT tasks.body, goals.name FROM tasks FULL OUTER JOIN goals ON goals.id = tasks.goal_id;

   -- 34. Find the tasks for every user
   SELECT tasks.body, users.name FROM tasks FULL OUTER JOIN users ON users.id = tasks.user_id;
 
   -- 35. Find the names of the logged habits
   SELECT habits.name, habit_log.date_completed FROM habits LEFT OUTER JOIN habit_log ON habits.id = habit_log.habit_id;

 -- ============================== Aggregate Functions =============================
   -- MAX, MIN, SUM, AVG, COUNT
   -- using GROUP BY
   -- using HAVING

   -- 36. SUM: Find Total XP Earned by ALL Users that are level 1.
   SELECT sum(xp) FROM users GROUP BY level HAVING level = 1;  

   -- 37. MAX: Find the max level of all users
   SELECT max(level) AS max_level FROM users;  

   -- 38. COUNT: Find the number of tasks for each goal.
   SELECT goal_id, COUNT(id) FROM tasks GROUP BY goal_id HAVING goal_id IN (SELECT id FROM goals) ORDER BY goal_id;

   -- 39. AVG: Find the average xp for all the users at every level greater than 1
   SELECT level, avg(xp) FROM users GROUP BY level HAVING level > 1 ORDER BY level;
   
   -- 40. MIN: Find the minimum goal deadline for Sam
   SELECT user_id, min(deadline) AS min_deadline FROM GOALS GROUP BY user_id HAVING user_id=1;
 
 -- ============================== Database Modification =============================

   -- 41. Insert a note
   INSERT INTO notes(title, user_id, body, date_created) VALUES('Friend''s Birthdays',1,'Jordan: 07/12, Jocko: 04/1, Joe: 12/31', '2018-05-05'); 
   
   -- 42. Insert into a table from subquery
   INSERT INTO earned(user_id, challenge_id) (select id, 6 from users);

   -- 43. Delete Sam's notes
   DELETE FROM notes WHERE user_id=1;
  
   -- 44. Delete tasks for which their goals are complete
   DELETE FROM tasks WHERE goal_id IN (SELECT id FROM goals WHERE is_complete='t');

   -- 45. Update Tasks to Complete if their goals is complete
   UPDATE tasks SET is_complete = 't' WHERE goal_id IN(SELECT id FROM goals WHERE is_complete='t') AND goal_id IS NOT NULL;

   -- 46. Update Sam's Email
   UPDATE users SET email='samuel@astate.edu' WHERE name='Sam';
  

 -- ============================== View =============================
   -- 47. Create view for Sam's goals
   create view sams_goals as (select * from goals where user_id in(select id from users where name='Sam'));
 -- ============================== PSM =============================
   -- 48. Functions
   -- Function that's called to give users experience points  
   CREATE OR REPLACE FUNCTION earn_xp()
     RETURNS trigger AS
     $BODY$
     BEGIN 
	UPDATE users SET xp = xp+TG_ARGV[0]::integer WHERE id=new.user_id;
        RETURN NEW;
     END;
     $BODY$
     LANGUAGE plpgsql VOLATILE
     COST 100;

   -- Function to level a user up and show that they completed a challenge
   CREATE OR REPLACE FUNCTION level_up()
	RETURNS trigger AS
	$BODY$
	BEGIN 
		IF old.level < 2 AND new.xp >= 100 AND new.xp < 250 THEN
			UPDATE users SET level = 2 WHERE id=new.id;
			INSERT INTO earned(user_id, challenge_id) VALUES(new.id, 2);
		ELSEIF old.level < 3 AND new.xp >= 250 AND new.xp < 400 THEN
			UPDATE users SET level = 3 WHERE id=new.id;
			INSERT INTO earned(user_id, challenge_id) VALUES(new.id, 3);
		ELSEIF old.level < 4 AND new.xp >= 400 AND new.xp < 600 THEN
			UPDATE users SET level = 4 WHERE id=new.id;
			INSERT INTO earned(user_id, challenge_id) VALUES(new.id, 4);
		ELSEIF old.level < 5 AND new.xp >= 600 AND new.xp < 900 THEN
			UPDATE users SET level = 5 WHERE id=new.id;
			INSERT INTO earned(user_id, challenge_id) VALUES(new.id, 5);
		ELSEIF old.level < 6 AND new.xp >= 900 AND new.xp < 1400 THEN
			UPDATE users SET level = 6 WHERE id=new.id;
			INSERT INTO earned(user_id, challenge_id) VALUES(new.id, 6);
		ELSEIF old.level < 7 AND new.xp >= 1400 AND new.xp < 2000 THEN
			UPDATE users SET level = 7 WHERE id=new.id;
			INSERT INTO earned(user_id, challenge_id) VALUES(new.id, 7);
		ELSEIF old.level < 8 AND new.xp >= 2000 AND new.xp < 3000 THEN
			UPDATE users SET level = 8 WHERE id=new.id;
			INSERT INTO earned(user_id, challenge_id) VALUES(new.id, 8);
		ELSEIF old.level < 9 AND new.xp >= 3000 AND new.xp < 4500 THEN
			UPDATE users SET level = 9 WHERE id=new.id;
			INSERT INTO earned(user_id, challenge_id) VALUES(new.id, 9);
		ELSEIF old.level < 10 AND new.xp >= 4500  THEN
			UPDATE users SET level = 10 WHERE id=new.id;
			INSERT INTO earned(user_id, challenge_id) VALUES(new.id, 10);
		ELSE
		END IF;
		RETURN NEW;
	END;
	$BODY$
	LANGUAGE plpgsql VOLATILE
	COST 100;

   -- 49. Triggers
   -- Trigger to give a user xp for creating a note
   CREATE TRIGGER note_xp
	AFTER INSERT ON notes
	FOR EACH ROW
	EXECUTE PROCEDURE earn_xp(5);

   -- Trigger to give a user xp for logging a habit
   CREATE TRIGGER habit_xp
	AFTER INSERT ON habit_log
	FOR EACH ROW
	EXECUTE PROCEDURE earn_xp(10);

   -- Trigger to give a user xp for completing a task
   CREATE TRIGGER task_xp
	AFTER UPDATE ON tasks
	FOR EACH ROW
	WHEN (new.is_complete = 't')
	EXECUTE PROCEDURE earn_xp(15);

   -- Trigger to give a user xp for completing a goal 
   CREATE TRIGGER goal_xp
	AFTER UPDATE ON goals
	FOR EACH ROW
	WHEN (new.is_complete = 't')
	EXECUTE PROCEDURE earn_xp(25);

   -- Trigger to level a user up
   CREATE TRIGGER level_up
	AFTER UPDATE ON users
	FOR EACH ROW
	WHEN (old.xp < new.xp)
	EXECUTE PROCEDURE level_up();

 -- ============================== Constraints =============================
   -- 50. Re-do your CREATE TABLE to have the constraints (at least one for each kind of constraints below)
   -- primary key 
	--DONE: On all tables.
   -- foreign key 
	--DONE: On goals, tasks, habits, habit_log, notes, friends, and earned tables.
   -- attribute constraint
	--DONE: On users and tasks tables.
   -- tuple constraint
	--DONE: On users and friends tables.
   

-- =============================== relational algebra
   --51. one relational algebra
   --52. one relational algebra tree
   --53. functional dependencies for each table
   --54. Indicate 3NF or BCNF or 4NF for each table
   --55. One simple interface to access your data from class machine. (An example will be provided.) 
