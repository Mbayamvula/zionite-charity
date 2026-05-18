-- Zionite Charity - Sample Data
-- Additional sample data for testing and demonstration

USE zionite_charity;

-- Insert Additional Volunteers
INSERT INTO volunteers (first_name, last_name, email, phone, address, city, country, skills, availability, motivation, status) VALUES
('John', 'Smith', 'john.smith@email.com', '+1-555-0101', '123 Main St', 'New York', 'USA', 'Counseling, Event Planning', 'Weekends', 'I want to help those in need in my community.', 'approved'),
('Emily', 'Johnson', 'emily.j@email.com', '+1-555-0102', '456 Oak Ave', 'Los Angeles', 'USA', 'Teaching, Childcare', 'Evenings', 'I have experience working with children and want to support orphanages.', 'approved'),
('Michael', 'Brown', 'm.brown@email.com', '+1-555-0103', '789 Pine Rd', 'Chicago', 'USA', 'Medical, First Aid', 'Flexible', 'As a healthcare professional, I want to provide medical support.', 'pending'),
('Sarah', 'Davis', 'sarah.d@email.com', '+1-555-0104', '321 Elm St', 'Houston', 'USA', 'Cooking, Food Service', 'Weekdays', 'I want to help with food distribution programs.', 'approved'),
('David', 'Wilson', 'd.wilson@email.com', '+1-555-0105', '654 Maple Dr', 'Phoenix', 'USA', 'IT, Web Development', 'Weekends', 'I can help with technical support and website maintenance.', 'pending'),
('Jennifer', 'Taylor', 'j.taylor@email.com', '+1-555-0106', '987 Cedar Ln', 'Philadelphia', 'USA', 'Teaching, Tutoring', 'Afternoons', 'I want to help children with education.', 'approved'),
('Robert', 'Anderson', 'r.anderson@email.com', '+1-555-0107', '159 Birch Blvd', 'San Antonio', 'USA', 'Construction, Repairs', 'Flexible', 'I can help with maintenance and renovation projects.', 'pending'),
('Lisa', 'Thomas', 'l.thomas@email.com', '+1-555-0108', '753 Spruce Way', 'San Diego', 'USA', 'Accounting, Finance', 'Weekends', 'I want to help with financial management and transparency.', 'approved');

-- Insert Additional Donations
INSERT INTO donations (donor_name, email, phone, amount, donation_type, payment_method, purpose, is_anonymous, message, status) VALUES
('Anonymous Donor', '', '', 500.00, 'one-time', 'credit-card', '', 1, 'For the orphanage renovation project.', 'completed'),
('Corporate Sponsor Inc.', 'contact@corporatesponsor.com', '+1-555-0201', 5000.00, 'one-time', 'bank-transfer', 'general', 0, 'Corporate sponsorship for 2024 programs.', 'completed'),
('Mary Johnson', 'mary.j@email.com', '+1-555-0202', 100.00, 'monthly', 'paypal', 'food-assistance', 0, 'Monthly donation for food distribution.', 'completed'),
('James Wilson', 'j.wilson@email.com', '+1-555-0203', 250.00, 'one-time', 'credit-card', 'hospital-visits', 0, 'In memory of my mother who received care.', 'completed'),
('Anonymous', '', '', 75.00, 'one-time', 'credit-card', 'clothing', 1, '', 'completed'),
('The Smith Foundation', 'info@smithfoundation.org', '+1-555-0204', 10000.00, 'one-time', 'bank-transfer', 'general', 0, 'Grant for humanitarian activities.', 'completed'),
('Patricia Brown', 'p.brown@email.com', '+1-555-0205', 50.00, 'monthly', 'credit-card', 'elderly-care', 0, 'Monthly support for elderly care programs.', 'completed'),
('Richard Miller', 'r.miller@email.com', '+1-555-0206', 150.00, 'one-time', 'paypal', 'orphanage-support', 0, 'For the children at Sunshine Orphanage.', 'completed');

-- Insert Additional Projects
INSERT INTO projects (title, description, category, location, start_date, end_date, budget, status, featured) VALUES
('Mobile Health Clinic', 'Launching a mobile health clinic to provide basic medical services to underserved communities and rural areas with limited healthcare access.', 'Healthcare', 'Rural Communities', '2024-06-01', '2024-12-31', 120000.00, 'upcoming', 1),
('Youth Mentorship Program', 'A comprehensive mentorship program pairing at-risk youth with positive role models to provide guidance, support, and opportunities for personal growth.', 'Education', 'Community Centers', '2024-07-01', '2025-06-30', 45000.00, 'upcoming', 0),
('Clean Water Initiative', 'Installing water purification systems in communities lacking access to clean drinking water, improving health and quality of life.', 'Infrastructure', 'Remote Villages', '2024-08-01', '2024-11-30', 80000.00, 'upcoming', 0),
('Skills Training Workshop', 'Providing vocational training and skills development workshops to help individuals gain employment and become self-sufficient.', 'Education', 'Training Center', '2024-05-01', '2024-10-31', 35000.00, 'ongoing', 1),
('Disaster Relief Fund', 'Emergency fund and resources for immediate response to natural disasters and humanitarian crises in affected regions.', 'Emergency', 'Multiple Locations', '2024-01-01', '2024-12-31', 200000.00, 'ongoing', 0),
('Community Garden Project', 'Creating community gardens to provide fresh produce for families in need and promote sustainable living practices.', 'Food Assistance', 'Urban Areas', '2024-04-01', '2024-09-30', 25000.00, 'ongoing', 0);

-- Insert Additional Reports
INSERT INTO reports (title, description, report_type, year, quarter, file_path, file_size, published_date, status) VALUES
('Financial Statement Q1 2024', 'Quarterly financial statement for the first quarter of 2024 showing income, expenses, and fund allocation.', 'financial', 2024, 1, 'financial_q1_2024.pdf', 1.8, '2024-04-15', 'published'),
('Project Impact Report 2023', 'Detailed report on the impact and outcomes of all projects completed in 2023.', 'project', 2023, 0, 'project_impact_2023.pdf', 3.5, '2024-02-01', 'published'),
('Quarterly Report Q3 2023', 'Third quarter 2023 report covering activities, achievements, and financial summary.', 'quarterly', 2023, 3, 'quarterly_q3_2023.pdf', 2.1, '2023-10-15', 'published'),
('Volunteer Program Review', 'Comprehensive review of volunteer programs, recruitment, retention, and impact.', 'annual', 2023, 0, 'volunteer_review_2023.pdf', 2.8, '2024-01-20', 'published');

-- Insert Additional Partners
INSERT INTO partners (name, logo, website, description, partnership_type, status, contact_person, contact_email) VALUES
('HealthFirst Foundation', 'healthfirst_logo.png', 'https://healthfirst.org', 'Healthcare foundation supporting medical missions and health education programs.', 'collaborator', 'active', 'Dr. Sarah Chen', 'schen@healthfirst.org'),
('EduCare International', 'educare_logo.png', 'https://educare.org', 'International education organization focused on providing quality education to underprivileged children.', 'collaborator', 'active', 'Mark Thompson', 'mthompson@educare.org'),
('BuildHope Construction', 'buildhope_logo.png', 'https://buildhope.com', 'Construction company providing pro bono services for community building projects.', 'sponsor', 'active', 'James Wilson', 'jwilson@buildhope.com'),
('MediaOne News Network', 'mediaone_logo.png', 'https://mediaone.news', 'News network helping raise awareness about humanitarian causes and charity work.', 'media', 'active', 'Lisa Anderson', 'landerson@mediaone.news'),
('FoodBank Alliance', 'foodbank_logo.png', 'https://foodbankalliance.org', 'Network of food banks working together to fight hunger in communities.', 'collaborator', 'active', 'Robert Martinez', 'rmartinez@foodbankalliance.org'),
('TechForGood Foundation', 'techforgood_logo.png', 'https://techforgood.org', 'Technology foundation providing digital solutions and IT support for non-profits.', 'donor', 'active', 'David Kim', 'dkim@techforgood.org');

-- Insert Additional Contact Messages
INSERT INTO contact_messages (name, email, phone, subject, message, status) VALUES
('Alice Cooper', 'alice.cooper@email.com', '+1-555-0301', 'Volunteer Inquiry', 'I am interested in volunteering for the hospital visit program. Please let me know how I can get started.', 'read'),
('Bob Martin', 'bob.martin@email.com', '+1-555-0302', 'Donation Question', 'I would like to make a large donation but have some questions about how the funds will be used. Can someone contact me?', 'replied'),
('Carol White', 'carol.white@email.com', '+1-555-0303', 'Partnership Proposal', 'Our company is interested in partnering with Zionite Charity for our CSR program. Please send partnership information.', 'read'),
('Daniel Harris', 'd.harris@email.com', '+1-555-0304', 'General Inquiry', 'I heard about your organization from a friend and wanted to learn more about your services.', 'unread'),
('Emma Clark', 'emma.clark@email.com', '+1-555-0305', 'Volunteer Application Follow-up', 'I submitted a volunteer application last week and wanted to check on its status.', 'unread'),
('Frank Lewis', 'f.lewis@email.com', '+1-555-0306', 'Report Request', 'I would like to request a copy of your annual report for my research project.', 'replied'),
('Grace Young', 'grace.young@email.com', '+1-555-0307', 'Event Inquiry', 'Are there any upcoming events where I can participate or volunteer?', 'read'),
('Henry King', 'h.king@email.com', '+1-555-0308', 'Feedback', 'I recently donated and wanted to share how impressed I am with your transparency and impact.', 'replied');

-- Insert Additional Testimonials
INSERT INTO testimonials (name, role, image, testimonial, rating, status) VALUES
('Robert Martinez', 'Community Leader', '', 'Zionite Charity has transformed our community through their consistent support and dedication. The impact is visible everywhere.', 5, 'active'),
('Jennifer Adams', 'Volunteer Coordinator', '', 'Working with Zionite Charity has been an incredible experience. Their commitment to helping others is truly inspiring.', 5, 'active'),
('Dr. William Chen', 'Medical Director', '', 'The hospital visit program has brought so much comfort to our patients. The volunteers are compassionate and caring.', 5, 'active'),
('Maria Rodriguez', 'Beneficiary', '', 'When I lost my job, Zionite Charity helped my family with food and emotional support. They gave us hope when we needed it most.', 5, 'active');
