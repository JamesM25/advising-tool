-- Drop the tables if they already exist
DROP TABLE IF EXISTS Classes;
DROP TABLE IF EXISTS Prerequisites;

-- Create tables
CREATE TABLE Classes (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(255) NOT NULL,
    Priority INT NOT NULL
);

CREATE TABLE Prerequisites (
    -- Must take class with ID PrerequisiteID BEFORE taking class with ID ClassID.
    PrerequisiteID INT NOT NULL,
    ClassID INT NOT NULL,

    FOREIGN KEY (PrerequisiteID) REFERENCES Classes(ID),
    FOREIGN KEY (ClassID) REFERENCES Classes(ID),

    PRIMARY KEY (ClassID, PrerequisiteID)
);

-- Insert default data
INSERT INTO Classes (Name, Priority) VALUES
    ('MATH97', -2),
    ('ENG101', -1),
    ('ENG126/127/128/235', -1),
    ('MATH141/147', -1),
    ('MATH146/256', -1),
    ('CMST210/220/230/238', -1),
    ('LAB SCIENCE', -1),
    ('SDEV201', 0),
    ('SDEV106', 0),
    ('SDEV117', 0),
    ('CS108/109', 0),
    ('SDEV121', 0),
    ('SDEV218', 0),
    ('SDEV219', 0),
    ('SDEV220', 0),
    ('SDEV280', 2);

INSERT INTO Prerequisites (PrerequisiteID, ClassID) VALUES
    ((SELECT ID FROM Classes WHERE Name='MATH97'), (SELECT ID FROM Classes WHERE Name='MATH141/147')),
    ((SELECT ID FROM Classes WHERE Name='MATH97'), (SELECT ID FROM Classes WHERE Name='MATH146/256')),
    ((SELECT ID FROM Classes WHERE Name='MATH97'), (SELECT ID FROM Classes WHERE Name='CS108/109')),
    ((SELECT ID FROM Classes WHERE Name='MATH97'), (SELECT ID FROM Classes WHERE Name='SDEV218')),
    ((SELECT ID FROM Classes WHERE Name='ENG101'), (SELECT ID FROM Classes WHERE Name='ENG126/127/128/235')),
    ((SELECT ID FROM Classes WHERE Name='CS108/109'), (SELECT ID FROM Classes WHERE Name='SDEV121')),
    ((SELECT ID FROM Classes WHERE Name='SDEV106'), (SELECT ID FROM Classes WHERE Name='SDEV117')),
    ((SELECT ID FROM Classes WHERE Name='SDEV218'), (SELECT ID FROM Classes WHERE Name='SDEV219')),
    ((SELECT ID FROM Classes WHERE Name='SDEV219'), (SELECT ID FROM Classes WHERE Name='SDEV220'));