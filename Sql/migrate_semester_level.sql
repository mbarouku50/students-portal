-- Migration Script: Rename and migrate document tables to new semester/level format

-- 1. Create new tables for each semester and level
CREATE TABLE IF NOT EXISTS sem1_certificate_documents LIKE first_year_sem1_documents;
CREATE TABLE IF NOT EXISTS sem2_certificate_documents LIKE first_year_sem2_documents;
CREATE TABLE IF NOT EXISTS sem1_diploma1_documents LIKE first_year_sem1_documents;
CREATE TABLE IF NOT EXISTS sem2_diploma1_documents LIKE first_year_sem2_documents;
CREATE TABLE IF NOT EXISTS sem1_diploma2_documents LIKE first_year_sem1_documents;
CREATE TABLE IF NOT EXISTS sem2_diploma2_documents LIKE first_year_sem2_documents;
CREATE TABLE IF NOT EXISTS sem1_bachelor1_documents LIKE first_year_sem1_documents;
CREATE TABLE IF NOT EXISTS sem2_bachelor1_documents LIKE first_year_sem2_documents;
CREATE TABLE IF NOT EXISTS sem1_bachelor2_documents LIKE first_year_sem1_documents;
CREATE TABLE IF NOT EXISTS sem2_bachelor2_documents LIKE first_year_sem2_documents;
CREATE TABLE IF NOT EXISTS sem1_bachelor3_documents LIKE first_year_sem1_documents;
CREATE TABLE IF NOT EXISTS sem2_bachelor3_documents LIKE first_year_sem2_documents;

-- 2. Migrate data from old tables to new tables based on level
INSERT INTO sem1_certificate_documents SELECT * FROM first_year_sem1_documents WHERE level = 'certificate';
INSERT INTO sem2_certificate_documents SELECT * FROM first_year_sem2_documents WHERE level = 'certificate';
INSERT INTO sem1_diploma1_documents SELECT * FROM first_year_sem1_documents WHERE level = 'diploma1';
INSERT INTO sem2_diploma1_documents SELECT * FROM first_year_sem2_documents WHERE level = 'diploma1';
INSERT INTO sem1_diploma2_documents SELECT * FROM first_year_sem1_documents WHERE level = 'diploma2';
INSERT INTO sem2_diploma2_documents SELECT * FROM first_year_sem2_documents WHERE level = 'diploma2';
INSERT INTO sem1_bachelor1_documents SELECT * FROM first_year_sem1_documents WHERE level = 'bachelor1';
INSERT INTO sem2_bachelor1_documents SELECT * FROM first_year_sem2_documents WHERE level = 'bachelor1';
INSERT INTO sem1_bachelor2_documents SELECT * FROM first_year_sem1_documents WHERE level = 'bachelor2';
INSERT INTO sem2_bachelor2_documents SELECT * FROM first_year_sem2_documents WHERE level = 'bachelor2';
INSERT INTO sem1_bachelor3_documents SELECT * FROM first_year_sem1_documents WHERE level = 'bachelor3';
INSERT INTO sem2_bachelor3_documents SELECT * FROM first_year_sem2_documents WHERE level = 'bachelor3';

-- 3. Drop old tables (optional, uncomment if ready)
-- DROP TABLE first_year_sem1_documents;
-- DROP TABLE first_year_sem2_documents;
-- DROP TABLE second_year_sem1_documents;
-- DROP TABLE second_year_sem2_documents;
-- DROP TABLE third_year_sem1_documents;
-- DROP TABLE third_year_sem2_documents;
-- DROP TABLE fourth_year_sem1_documents;
-- DROP TABLE fourth_year_sem2_documents;
