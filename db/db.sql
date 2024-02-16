-- Drop the tables if they already exist
DROP TABLE IF EXISTS Classes;
DROP TABLE IF EXISTS Prerequisites;

-- Create tables
CREATE TABLE Classes (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(255) NOT NULL,
    GroupNum INT
);

CREATE TABLE Prerequisites (
    -- Must take class with ID PrerequisiteID BEFORE taking class with ID ClassID.
    PrerequisiteID INT NOT NULL,
    ClassID INT NOT NULL,
    GroupNum INT,

    FOREIGN KEY (PrerequisiteID) REFERENCES Classes(ID),
    FOREIGN KEY (ClassID) REFERENCES Classes(ID),

    PRIMARY KEY (ClassID, PrerequisiteID)
);

-- Insert default data
INSERT INTO Classes (Name, GroupNum) VALUES
    ('MATH97', NULL),

    ('ENG101', NULL),

    ('ENG126', 1),
    ('ENG127', 1),
    ('ENG128', 1),
    ('ENG235', 1),

    ('MATH141', 2),
    ('MATH147', 2),

    ('MATH146', 3),
    ('MATH256', 3),

    ('CMST210', 4),
    ('CMST220', 4),
    ('CMST230', 4),
    ('CMST238', 4),

    ('LAB SCIENCE', NULL),

    ('SDEV201', NULL),

    ('SDEV106', NULL),

    ('SDEV117', NULL),

    ('CS108', 5),
    ('CS109', 5),

    ('SDEV121', NULL),

    ('SDEV218', NULL),

    ('SDEV219', NULL),

    ('SDEV220', NULL),

    ('SDEV280', NULL);

INSERT INTO Prerequisites (PrerequisiteID, ClassID, GroupNum) VALUES
    ((SELECT ID FROM Classes WHERE Name='MATH97'), (SELECT ID FROM Classes WHERE Name='MATH141'), NULL),
    ((SELECT ID FROM Classes WHERE Name='MATH97'), (SELECT ID FROM Classes WHERE Name='MATH146'), NULL),
    ((SELECT ID FROM Classes WHERE Name='MATH97'), (SELECT ID FROM Classes WHERE Name='MATH147'), NULL),
    ((SELECT ID FROM Classes WHERE Name='MATH97'), (SELECT ID FROM Classes WHERE Name='MATH256'), NULL),

    ((SELECT ID FROM Classes WHERE Name='ENG101'), (SELECT ID FROM Classes WHERE Name='ENG126'), NULL),
    ((SELECT ID FROM Classes WHERE Name='ENG101'), (SELECT ID FROM Classes WHERE Name='ENG127'), NULL),
    ((SELECT ID FROM Classes WHERE Name='ENG101'), (SELECT ID FROM Classes WHERE Name='ENG128'), NULL),
    ((SELECT ID FROM Classes WHERE Name='ENG101'), (SELECT ID FROM Classes WHERE Name='ENG235'), NULL),

    ((SELECT ID FROM Classes WHERE Name='MATH97'), (SELECT ID FROM Classes WHERE Name='SDEV106'), NULL),
    ((SELECT ID FROM Classes WHERE Name='SDEV106'), (SELECT ID FROM Classes WHERE Name='SDEV117'), NULL),

    ((SELECT ID FROM Classes WHERE Name='MATH97'), (SELECT ID FROM Classes WHERE Name='SDEV218'), NULL),
    ((SELECT ID FROM Classes WHERE Name='SDEV218'), (SELECT ID FROM Classes WHERE Name='SDEV219'), NULL),
    ((SELECT ID FROM Classes WHERE Name='SDEV219'), (SELECT ID FROM Classes WHERE Name='SDEV220'), NULL),

    ((SELECT ID FROM Classes WHERE Name='MATH97'), (SELECT ID FROM Classes WHERE Name='CS108'), NULL),
    ((SELECT ID FROM Classes WHERE Name='MATH97'), (SELECT ID FROM Classes WHERE Name='CS109'), NULL),

    ((SELECT ID FROM Classes WHERE Name='CS108'), (SELECT ID FROM Classes WHERE Name='SDEV121'), 1),
    ((SELECT ID FROM Classes WHERE Name='CS109'), (SELECT ID FROM Classes WHERE Name='SDEV121'), 1);