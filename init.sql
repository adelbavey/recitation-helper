
-- Uncomment for resetting tables

/*
DROP TABLE Course CASCADE;
DROP TABLE Recitation CASCADE;
DROP TABLE RecGroup CASCADE;
DROP TABLE Problem CASCADE;
DROP TABLE SubProblem CASCADE;
DROP TABLE Student CASCADE;
DROP TABLE CourseRec;
DROP TABLE RecInstances;
DROP TABLE HasProblem;
DROP TABLE SubProblems;
DROP TABLE Solved;
DROP TABLE Result;
*/

-- Enitity sets
CREATE TABLE Course (
    Cid integer PRIMARY KEY,
    Name text NOT NULL
);

CREATE TABLE Recitation (
    Rid integer PRIMARY KEY,
    Number integer NOT NULL
);

CREATE TABLE RecGroup (
    Letter character PRIMARY KEY
);

CREATE TABLE Problem (
    Pid integer PRIMARY KEY,
    Number integer NOT NULL,
    Condition integer,
    Points integer NOT NULL
);
CREATE TABLE SubProblem (
    Spid integer PRIMARY KEY,
    Letter character NOT NULL
);


CREATE TABLE Student (
    Id integer PRIMARY KEY,
    Name text NOT NULL
);

-- Relation sets
CREATE TABLE CourseRec (
    Course integer REFERENCES Course,
    Recitation integer REFERENCES Recitation
);

CREATE TABLE RecInstances (
    Recitation integer REFERENCES Recitation,
    RecGroup character REFERENCES RecGroup
);

CREATE TABLE HasProblem (
    Recitation integer REFERENCES Recitation,
    Problem integer REFERENCES Problem
);

CREATE TABLE SubProblems (
    Problem integer REFERENCES Problem,
    SubProblem integer REFERENCES SubProblem
);

CREATE TABLE Solved (
    Student integer REFERENCES Student,
    Recitation integer REFERENCES Recitation,
    RecGroup character REFERENCES Recgroup,
    SubProblem integer REFERENCES SubProblem,
    Shown boolean NOT NULL,
    UNIQUE (Student, SubProblem)
);

CREATE TABLE Result (
    Student integer REFERENCES Student,
    Recitation integer REFERENCES Recitation,
    Res integer,
    UNIQUE (Student, Recitation)
);

-- Insert data

--Entities
INSERT INTO Course (Cid, Name) VALUES
    (1, 'Databasteknik'),
    (2, 'Mjukvarukonstruktion');

INSERT INTO Recitation (Rid, Number) VALUES
    (1, 1),
    (2, 2);

INSERT INTO RecGroup (Letter) VALUES
    ('A'),
    ('B');

INSERT INTO Problem (Pid, Number, Condition, Points) VALUES
    (1, 1, 1, 11),
    (2, 2, 2, 11),
    (3, 3, 3, 11),
    (4, 1, 2, 11),
    (5, 2, 2, 11),
    (6, 3, 3, 11);

INSERT INTO SubProblem (Spid, Letter) VALUES
    (1, 'a'),
    (2, 'b'),
    (3, 'c'),
    (4, 'a'),
    (5, 'b'),
    (6, 'c'),
    (7, 'a'),
    (8, 'b'),
    (9, 'c'),
    (10, 'a'),
    (11, 'b'),
    (12, 'c'),
    (13, 'a'),
    (14, 'b'),
    (15, 'c'),
    (16, 'a'),
    (17, 'b'),
    (18, 'c');

INSERT INTO Student (Id, Name) VALUES
    (1, 'Adel Bavey'),
    (2, 'Felix Kollin'),
    (3, 'Captain America');

--Relations

INSERT INTO CourseRec (Course, Recitation) VALUES
    (1, 1),
    (1, 2);

INSERT INTO RecInstances (Recitation, RecGroup) VALUES
    (1, 'A'),
    (1, 'B'),
    (2, 'A'),
    (2, 'B');

INSERT INTO HasProblem (Recitation, Problem) VALUES
    (1, 1),
    (1, 2),
    (1, 3),
    (2, 4),
    (2, 5),
    (2, 6);

INSERT INTO SubProblems (Problem, SubProblem) VALUES
    (1, 1),
    (1, 2),
    (1, 3),
    (2, 4),
    (2, 5),
    (2, 6),
    (3, 7),
    (3, 8),
    (3, 9),
    (4, 10),
    (4, 11),
    (4, 12),
    (5, 13),
    (5, 14),
    (5, 15),
    (6, 16),
    (6, 17),
    (6, 18);