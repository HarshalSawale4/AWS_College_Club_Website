-- ======================================================
-- Database: aws_club
-- Complete schema for AWS Cloud Club TCOER website
-- ======================================================

CREATE DATABASE IF NOT EXISTS aws_club;
USE aws_club;

-- ======================================================
-- Table: teams (stores team categories for members)
-- ======================================================
CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(10) DEFAULT '🤝',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ======================================================
-- Table: members (club members with team association)
-- ======================================================
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(100) NOT NULL,
    bio TEXT,
    team_id INT,
    linkedin VARCHAR(255),
    instagram VARCHAR(255),
    image_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL
);

-- ======================================================
-- Table: events (public events with optional image)
-- ======================================================
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    event_date VARCHAR(50) NOT NULL,
    location VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    register_link VARCHAR(500) DEFAULT '#',
    icon_emoji VARCHAR(10) DEFAULT '📅',
    image_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ======================================================
-- Table: stats (statistics displayed on home page)
-- ======================================================
CREATE TABLE stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    value INT NOT NULL,
    icon VARCHAR(10) NOT NULL,
    display_order INT DEFAULT 0
);

-- ======================================================
-- Table: resources (learning resources section)
-- ======================================================
CREATE TABLE resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    link VARCHAR(500) DEFAULT '#',
    icon VARCHAR(10) DEFAULT '📘'
);

-- ======================================================
-- Table: contact (single row contact information)
-- ======================================================
CREATE TABLE contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    social_message VARCHAR(255),
    phone_info VARCHAR(255)
);

-- ======================================================
-- INSERT DEFAULT DATA (matches original static website)
-- ======================================================

-- Teams
INSERT INTO teams (name, description, icon) VALUES
('Core Leadership', 'President and Vice President', '👑'),
('Technical Team', 'Tech leads and certification experts', '⚙️'),
('Events & Outreach', 'Event planning and community outreach', '📢'),
('Support & Volunteers', 'Volunteers and faculty advisor', '🤝'),
('Social Media', 'Handles Instagram, LinkedIn, Twitter', '📱'),
('Marketing', 'Promotions, branding, and campaigns', '📈');

-- Members (team_id must match above IDs)
INSERT INTO members (name, role, bio, team_id, linkedin, instagram, image_url) VALUES
('Aditya Kulkarni', 'President & Cloud Lead', 'AWS Certified Solutions Architect | Leads overall club strategy.', 1, '#', '#', NULL),
('Priya Sharma', 'Vice President', 'Cloud Advocate | Women in Tech lead | Manages outreach.', 1, '#', '#', NULL),
('Rahul Joshi', 'Technical Lead', 'Full-stack & DevOps | Conducts AWS workshops.', 2, '#', '#', NULL),
('Neha Patil', 'Certification Lead', 'AWS Cloud Practitioner certified | Helps with exam prep.', 2, '#', '#', NULL),
('Simran Nair', 'Events & Outreach Lead', 'Community builder | Manages registrations and coordination.', 3, '#', '#', NULL),
('Kunal More', 'Social Media Manager', 'Content creator | Handles Instagram, LinkedIn, Twitter.', 5, '#', '#', NULL),
('Sakshi Singh', 'Volunteer Coordinator', 'Manages volunteer team and event execution.', 4, '#', '#', NULL),
('Prof. Mehta', 'Faculty Advisor', 'Dept. of Computer Science | Guides the club.', 4, '#', '#', NULL);

-- Events
INSERT INTO events (title, event_date, location, type, description, register_link, icon_emoji, image_url) VALUES
('AWS Student Community Day Pune 2025', '24 Jan, 2025', 'TCOER, Pune', 'Upcoming', 'Flagship gathering of AWS student builders, workshops, talks & networking.', '#', '📅', NULL),
('AWS Community Day & Hackathon', '24 Jul, 2025', 'TCOER, Pune', 'Featured', 'Hands-on hackathon, certification workshops, and expert mentorship.', '#', '🚀', NULL),
('Build with Amazon Bedrock & SageMaker', '10 Mar, 2025', 'Online + TCOER', 'AI/ML', 'Learn to build generative AI applications using AWS AI services.', '#', '🧠', NULL);

-- Stats
INSERT INTO stats (label, value, icon, display_order) VALUES
('Community Members', 125, '👥', 1),
('Events Organized', 12, '📅', 2),
('Certifications Earned', 28, '🎓', 3),
('Workshops', 8, '📄', 4),
('Projects Completed', 42, '🏗️', 5),
('Active Chapters', 5, '🌍', 6);

-- Resources
INSERT INTO resources (title, description, link, icon) VALUES
('AWS Skill Builder', 'Free digital training, learning plans, and exam readiness.', '#', '📘'),
('AWS Cloud Quest', 'Role-playing game to learn cloud skills in a fun way.', '#', '🎮'),
('Student Builder Program', 'Earn AWS credits, swag, and mentorship opportunities.', '#', '🏆');

-- Contact (single row)
INSERT INTO contact (email, social_message, phone_info) VALUES
('cloudclubstcoer@tcoer.com', 'Reach us through social media', 'Join us to grow your career');

-- ======================================================
-- Optional: Add indexes for performance
-- ======================================================
CREATE INDEX idx_members_team ON members(team_id);
CREATE INDEX idx_events_type ON events(type);
CREATE INDEX idx_stats_order ON stats(display_order);