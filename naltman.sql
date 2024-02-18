-- phpMyAdmin SQL Dump
-- version 5.2.1-1.fc38
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 22, 2023 at 05:34 AM
-- Server version: 10.5.21-MariaDB
-- PHP Version: 8.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fit2104_assignment_3`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `photo` text NOT NULL,
  `fname` char(50) NOT NULL,
  `lname` char(50) NOT NULL,
  `email` char(255) NOT NULL,
  `phone` char(12) NOT NULL,
  `suburb` varchar(250) NOT NULL,
  `address` varchar(250) NOT NULL,
  `recruitment` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `photo`, `fname`, `lname`, `email`, `phone`, `suburb`, `address`, `recruitment`) VALUES
(5, '1695360070_4753.jpg', 'Carl', 'Berry', 'sed.est.nunc@aol.couk', '0438132543', 'Springfield', '123 Main Street', 'Website'),
(10, '../clients_profiles/65058a3172061_michael-dam-mEZ3PoFGs_k-unsplash.jpg', 'Cedric', 'Langley', 'aliquet@protonmail.couk', '0495715683', 'Lara', '78 Stevens Street', ''),
(11, '../clients_profiles/65058a38db4dd_christopher-campbell-rDEOVtE7vOs-unsplash.jpg', 'Macaulay', 'Palmer', 'aliquet@protonmail.couk', '0495715683', 'Lara', '54 Ryrie Street', 'Facebook'),
(12, '../clients_profiles/65058eab67113_ali-morshedlou-WMD64tMfc4k-unsplash.jpg', 'Amity', 'Webb', 'eros.non@icloud.edu', '0468653660', 'Wyndham Vale', '73 Wydnham Street', 'Facebook'),
(14, '../clients_profiles/650675e2c4890_linkedin-sales-solutions-pAtA8xe_iVM-unsplash.jpg', 'Gerald', 'Agosta', 'g.agosta@gmail.com', '0475848575', 'Altona Meadows', '666 Redwood Road', 'Facebook'),
(15, '../clients_profiles/6507b70d6f695_charles-etoroma-95UF6LXe-Lo-unsplash.jpg', 'Keith', 'Hartman', 'nunc@hotmail.com', '0460985563', 'Clayton', '32 Heal Street', 'Website'),
(22, '../clients_profiles/65095ab586f98_juan-encalada-WC7KIHo13Fc-unsplash.jpg', 'Uma', 'Randall', 'libero@hotmail.net', '0527653148', 'Hollywood', '87 Newtown Lane', 'Website'),
(23, '1695360091_9756.jpg', 'Carl', 'Berry', 'sed.dictum@hotmail.net', '0581233664', 'Carlton', '45 Ryrie Street', ''),
(24, '1695360116_3581.jpg', 'Eloise', 'Park', 'per.inceptos@google.com', '0536823871', 'Blackborn', '88 Park Court', ''),
(25, '1695360218_5500.jpg', 'Peter', 'Snow', 'natoque.penatibus@hotmail.ca', '0533988872', 'Clayton', '54 Ryrie Street', '');

-- --------------------------------------------------------

--
-- Table structure for table `clients_organisations`
--

CREATE TABLE `clients_organisations` (
  `client_id` int(11) NOT NULL,
  `organisation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `clients_organisations`
--

INSERT INTO `clients_organisations` (`client_id`, `organisation_id`) VALUES
(5, 19),
(10, 2),
(10, 9),
(10, 10),
(10, 11),
(10, 14),
(11, 2),
(11, 3),
(11, 6),
(11, 11),
(11, 18),
(11, 22),
(12, 4),
(12, 13),
(12, 22),
(14, 2),
(14, 22),
(15, 12),
(22, 2),
(22, 3),
(22, 4),
(23, 13),
(24, 15),
(24, 16),
(24, 17),
(24, 19),
(25, 19);

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `fname` char(50) NOT NULL,
  `lname` char(50) NOT NULL,
  `email` char(255) NOT NULL,
  `phone` char(12) NOT NULL,
  `message` varchar(3000) DEFAULT NULL,
  `replied` tinyint(1) DEFAULT 0,
  `client_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `fname`, `lname`, `email`, `phone`, `message`, `replied`, `client_id`) VALUES
(11, 'Macaulay', 'Palmer', 'non.bibendum@aol.net', '0488583151', 'Hi, I\'m interested in joining your science project team. Can you provide more details?', 0, 11),
(12, 'Amity', 'Webb', 'eros.non@icloud.edu', '0468653660', 'I\'d love to contribute to your science project. How can I get involved?', 0, 12),
(13, 'Carl', 'Berry', 'sed.est.nunc@aol.couk', '0438132543', 'Could you please share information about your ongoing science research?', 0, 5),
(14, 'Hector', 'Boyer', 'scelerisque.neque@google.ca', '0525027555', 'I\'m a science enthusiast looking for research opportunities. Any openings?', 0, NULL),
(15, 'Uma', 'Randall', 'libero@hotmail.net', '0527653148', 'Interested in volunteering for your science project. What\'s the application process?', 1, NULL),
(16, 'Summer', 'Tyson', 'sed.molestie@yahoo.org', '0410583845', 'What qualifications do you require for participants in your science study?', 0, NULL),
(17, 'Moana', 'Cantrell', 'aliquam.fringilla@aol.edu', '0455557933', 'I\'m eager to learn and work on your science project. How can I apply?', 1, NULL),
(18, 'Riley', 'Knowles', 'eu.odio.tristique@hotmail.edu', '0517832557', 'Do you have any upcoming events or workshops related to your science research?', 1, NULL),
(19, 'Keith', 'Hartman', 'nunc@hotmail.com', '0460985563', 'I have a background in biology. Are there roles in your science team for me?', 1, 15),
(20, 'Cedric', 'Langley', 'aliquet@protonmail.couk', '0495715683', 'Curious about your research goals. Can you provide an overview?', 0, 10),
(21, 'Elliott', 'Mcdaniel', 'per.inceptos@google.com', '0536823871', 'I\'m passionate about environmental science. Any roles in that field?', 0, NULL),
(22, 'Fallon', 'Barton', 'sed.dictum@hotmail.net', '0581233664', 'Interested in science outreach. How can I assist with your community efforts?', 1, NULL),
(23, 'Avye', 'Park', 'in.faucibus@icloud.edu', '0578123789', 'I\'m a student looking for a science internship. Do you offer any?', 0, NULL),
(24, 'Peter', 'Snow', 'natoque.penatibus@hotmail.ca', '0533988872', 'Are there any specific skills or qualifications you\'re looking for in science recruits?', 0, NULL),
(25, 'Amal', 'Kim', 'montes.nascetur@icloud.com', '0435909666', 'I\'d like to learn more about your science project. Can we schedule a meeting?', 1, NULL),
(28, 'Gerald', 'Agosta', 'g.agosta@gmail.com', '0475848575', 'Hello, I\'d love to work with you!', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `organisations`
--

CREATE TABLE `organisations` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `website` varchar(2083) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `tech` varchar(1000) DEFAULT NULL,
  `industry` varchar(1000) DEFAULT NULL,
  `services` varchar(1000) DEFAULT NULL,
  `field` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `organisations`
--

INSERT INTO `organisations` (`id`, `name`, `website`, `description`, `tech`, `industry`, `services`, `field`) VALUES
(2, 'Tazzy', 'http://tazzy.com', 'a space exploration startup with plans for Mars colonization', 'Virtual Reality (VR) and Augmented Reality (AR)', 'Oil & Gas Production', 'Research and Development (R&D)', 'Robotics'),
(3, 'Mybuzz', 'https://csmonitor.com/mybuzz', 'a virtual reality studio crafting immersive VR content', 'Big Data', '', 'Customized Training and Workshops.', 'Artificial Intelligence'),
(4, 'Jabberbean', 'https://howstuffworks.com/jabberbean', 'an aerospace company specializing in satellite technology', 'Cloud Computing', 'Telecommunications Equipment', 'Space Research and Satellite Technology Services', 'Robotics'),
(5, 'Youbridge', 'https://youbridge.com', 'a big data analytics startup helping businesses make data-driven decisions', 'Automation', 'Property-Casualty Insurers', 'Data Analysis and Interpretation', 'Biotechnology'),
(6, 'Wikizz', 'https://wikizz.com', 'a renewable energy provider harnessing solar and wind power', 'Advanced Manufacturing.', 'Specialty Insurers', 'Biotechnology Research and Development', 'Machine Learning'),
(7, 'Yacero', 'https://yacero.com', 'a nanotechnology research institute advancing nanomaterials', 'Internet of Things (IoT)', '', 'Space Research and Satellite Technology Services', 'Biotechnology'),
(8, 'Roomm', 'https://roomm.com', 'a nanotechnology research institute advancing nanomaterials', 'Biotechnology', 'Savings Institutions', 'Technical Consulting', 'Data Science'),
(9, 'Blogspan', 'https://blogspan.com', 'a virtual assistant software company enhancing productivity', 'Blockchain', '', 'Biotechnology Research and Development', 'Genetics'),
(10, 'Twinte', 'https://twinte.com', 'a 3D printing manufacturer producing custom prototypes', 'Space Exploration Technologies', 'Telecommunications Equipment', 'Environmental Impact Assessments', 'Robotics'),
(11, 'InnoZ', 'https://innoz.com', 'a 3D printing manufacturer producing custom prototypes', 'Artificial Intelligence (AI)', 'Natural Gas Distribution', 'Cybersecurity Solutions', 'Virtual Reality (VR)'),
(12, 'Mita', 'https://mita.com', 'an AR advertising agency creating interactive campaigns', '3D Printing', 'Paper', 'and Customized Training and Workshops.', 'Biotechnology'),
(13, 'Ooba', 'https://ooba.com', 'a cybersecurity firm protecting businesses from digital threats', 'Virtual Reality (VR) and Augmented Reality (AR)', '', 'Environmental Impact Assessments', 'Automation'),
(14, 'Flashpoint', 'https://flashpoint.com', 'a fintech startup disrupting traditional banking with blockchain', 'Materials Science Technologies', '', 'Technical Consulting', 'Cybersecurity'),
(15, 'Skimia', 'https://harvard.com', 'a virtual reality studio crafting immersive VR content', 'Quantum Computing', 'Building operators', 'Nanotechnology Research and Applications', 'Nanotechnology'),
(16, 'Photolist', 'https://photolist.com', 'an e-commerce platform revolutionizing online shopping with AR', 'Data Analytics', 'Professional Services', 'IT Infrastructure Management', 'Materials Science'),
(17, 'Jamia', 'https://jamia.com', 'a quantum cryptography firm ensuring secure communications', 'Quantum Computing', 'Major Chemicals', 'Research and Development (R&D)', 'Advanced Manufacturing'),
(18, 'Jetwire', 'https://jetwire.com', 'a virtual reality studio crafting immersive VR content', 'Automation', 'EDP Services', 'Quality Assurance and Testing', 'Data Science'),
(19, 'Yozio', 'https://yozio.com', 'a cybersecurity firm protecting businesses from digital threats', 'Renewable Energy Technologies', 'Commercial Banks', 'Quality Assurance and Testing', 'Advanced Manufacturing'),
(20, 'Kimia', 'https://kimia.com', 'an e-commerce platform revolutionizing online shopping with AR', 'Space Exploration Technologies', 'Movies/Entertainment', 'Space Research and Satellite Technology Services', 'Materials Science'),
(21, 'Zoomdog', 'https://zoomdog.com', 'a gaming studio creating high-quality video games', 'Automation', 'Investment Bankers/Brokers/Service', 'IT Infrastructure Management', 'Renewable Energy'),
(22, 'Google', 'google.com', 'lkhsdghkdslhkgslhk', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `name` char(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `semester_year` varchar(10) NOT NULL,
  `strengths` varchar(1000) DEFAULT NULL,
  `weaknesses` varchar(1000) DEFAULT NULL,
  `proposal_path` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `client_id`, `name`, `description`, `semester_year`, `strengths`, `weaknesses`, `proposal_path`) VALUES
(15, 5, 'Investigating the Impact of Pollution on Water Quality in Urban Streams', 'dolor sit amet consectetuer adipiscing elit proin interdum mauris non ligula pellentesque ultrices phasellus id sapien in sapien iaculis congue vivamus metus arcu adipiscing molestie hendrerit at vulputate vitae nisl aenean lectus pellentesque eget nunc donec quis orci eget orci vehicula condimentum curabitur in libero ut massa volutpat convallis morbi odio odio elementum eu interdum eu tincidunt in leo maecenas pulvinar lobortis est phasellus sit amet erat nulla tempus vivamus in felis eu sapien cursus vestibulum proin eu mi nulla ac enim in tempor turpis nec euismod scelerisque quam turpis adipiscing lorem', 'S2 2023', 'vivamus metus arcu adipiscing molestie hendrerit at vulputate vitae nisl aenean lectus pellentesque eget nunc donec quis orci eget orci vehicula condimentum curabitur in libero ut massa', 'platea dictumst maecenas ut massa quis augue luctus tincidunt nulla mollis molestie lorem quisque ut erat curabitur gravida nisi at nibh in hac habitasse platea dictumst aliquam augue quam sollicitudin vitae consectetuer eget rutrum at lorem', '../projects_proposals/65078aaf2ee05_Trust the People! Populism and the Two Faces of Democracy (Political Studies, vol. 47, issue 1) (1999).pdf'),
(16, 10, 'Exploring the Microscopic World: A Study of Microorganisms in Local Ponds', 'nullam molestie nibh in lectus pellentesque at nulla suspendisse potenti cras in purus eu magna vulputate luctus cum sociis natoque penatibus et magnis dis parturient montes nascetur ridiculus mus vivamus vestibulum sagittis sapien cum sociis natoque penatibus et magnis dis parturient montes nascetur ridiculus mus etiam vel augue vestibulum rutrum rutrum neque aenean auctor gravida sem praesent id massa id nisl venenatis lacinia aenean sit amet justo morbi ut odio cras mi pede malesuada in imperdiet et commodo vulputate justo in blandit ultrices enim lorem ipsum dolor sit amet consectetuer adipiscing elit proin interdum mauris non ligula pellentesque ultrices phasellus id', 'S2 2022', 'erat tortor sollicitudin mi sit amet lobortis sapien sapien non mi integer ac neque duis bibendum', 'pulvinar lobortis est phasellus sit amet erat nulla tempus vivamus in felis eu sapien cursus vestibulum', '../projects_proposals/6507aa7a29363_singh2017.pdf'),
(17, 11, 'From Trash to Treasure: Recycling Plastics into Useful Products', 'ornare consequat lectus in est risus auctor sed tristique in tempus sit amet sem fusce consequat nulla nisl nunc nisl duis bibendum felis sed interdum venenatis turpis enim blandit mi in porttitor pede justo eu massa donec dapibus duis at velit eu est congue elementum in hac habitasse', 'S2 2066', 'ultrices vel augue vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae donec pharetra magna vestibulum aliquet ultrices erat tortor sollicitudin mi sit amet lobortis sapien sapien non mi integer', 'rhoncus dui vel sem sed sagittis nam congue risus semper porta volutpat quam pede lobortis ligula sit amet eleifend pede libero quis orci nullam molestie nibh in lectus pellentesque at nulla suspendisse potenti cras in purus eu magna vulputate luctus cum sociis natoque penatibus et magnis dis parturient', NULL),
(18, 12, 'Unraveling the Mysteries of DNA: A Hands-on Genetics Experiment', 'id nisl venenatis lacinia aenean sit amet justo morbi ut odio cras mi pede malesuada in imperdiet et commodo vulputate justo in blandit ultrices enim lorem ipsum dolor sit amet consectetuer adipiscing elit proin interdum mauris non ligula pellentesque ultrices phasellus id sapien in sapien', 'S1 2019', 'et tempus semper est quam pharetra magna ac consequat metus sapien ut nunc vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae mauris viverra diam vitae quam suspendisse potenti', 'ac leo pellentesque ultrices mattis odio donec vitae nisi nam ultrices libero non mattis pulvinar nulla pede ullamcorper augue a suscipit nulla elit ac nulla sed vel enim sit amet nunc viverra dapibus nulla suscipit ligula in lacus curabitur at ipsum ac tellus semper interdum mauris ullamcorper purus', '../projects_proposals/65078abfe71bd_kovats-2018-questioning-consensuses-right-wing-populism-anti-populism-and-the-threat-of-gender-ideology.pdf'),
(19, 5, 'Exploring the Microscopic World: A Study of Microorganisms in Local Ponds', 'eget semper rutrum nulla nunc purus phasellus in felis donec semper sapien a libero nam dui proin leo odio porttitor id consequat in consequat ut nulla sed accumsan felis ut at dolor quis odio consequat varius integer ac leo pellentesque ultrices mattis odio donec vitae nisi nam ultrices libero non mattis pulvinar nulla pede ullamcorper augue a suscipit nulla elit ac nulla sed vel enim sit amet nunc viverra dapibus nulla suscipit ligula in lacus curabitur at ipsum ac tellus semper interdum mauris ullamcorper purus sit amet nulla quisque', 'S1 2023', 'erat eros viverra eget congue eget semper rutrum nulla nunc purus', 'porttitor id consequat in consequat ut nulla sed accumsan felis ut at dolor quis odio consequat varius integer ac leo pellentesque ultrices mattis odio donec vitae nisi', NULL),
(20, 14, 'The Power of Magnetism: Building an Electromagnetic Levitation Device', 'vel nisl duis ac nibh fusce lacus purus aliquet at feugiat non pretium quis lectus suspendisse potenti in eleifend quam a odio in hac habitasse platea dictumst maecenas ut massa quis augue luctus tincidunt nulla mollis molestie lorem quisque ut erat curabitur gravida nisi at nibh in hac habitasse platea', 'S2 2042', 'nisl duis bibendum felis sed interdum venenatis turpis enim blandit mi in porttitor pede justo eu', 'vestibulum velit id pretium iaculis diam erat fermentum justo nec condimentum neque sapien placerat ante nulla justo aliquam quis turpis eget elit sodales', NULL),
(21, 10, 'Harnessing Solar Energy: Building an Efficient Solar Cell Prototype', 'est donec odio justo sollicitudin ut suscipit a feugiat et eros vestibulum ac est lacinia nisi venenatis tristique fusce congue diam id ornare imperdiet sapien urna pretium nisl ut volutpat sapien arcu sed augue aliquam erat volutpat in congue etiam justo etiam pretium iaculis justo in hac habitasse platea dictumst etiam faucibus cursus urna ut tellus nulla ut erat id mauris vulputate elementum nullam varius nulla facilisi cras non velit nec nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus', 'S1 2004', 'amet consectetuer adipiscing elit proin risus praesent lectus vestibulum quam sapien varius ut blandit non interdum in ante vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae duis faucibus', 'in purus eu magna vulputate luctus cum sociis natoque penatibus et magnis dis parturient montes nascetur ridiculus mus vivamus vestibulum sagittis sapien cum sociis natoque', '../projects_proposals/65095b1628639_buildinfo prerun name.txt'),
(22, 5, 'Robotics and Automation: Designing a Smart Home Assistant', 'cras pellentesque volutpat dui maecenas tristique est et tempus semper est quam pharetra magna ac consequat metus sapien ut nunc vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae mauris viverra diam vitae quam suspendisse potenti nullam porttitor', 'S2 1996', 'at turpis a pede posuere nonummy integer non velit donec diam neque vestibulum eget vulputate ut ultrices vel augue vestibulum ante ipsum primis in faucibus orci luctus et', 'odio curabitur convallis duis consequat dui nec nisi volutpat eleifend donec ut dolor morbi vel lectus in quam fringilla rhoncus mauris enim leo rhoncus sed vestibulum sit amet cursus id turpis integer aliquet massa id lobortis convallis tortor risus dapibus augue', '../projects_proposals/65078ad521f97_The Rise of Populism and the Crisis of Globalisation_ Brexit, Trump and Beyond (Irish Studies in International Affairs, vol. 28) (2017).pdf'),
(23, 12, 'The Effects of Different Soil Types on Plant Growth', 'urna pretium nisl ut volutpat sapien arcu sed augue aliquam erat volutpat in congue etiam justo etiam pretium iaculis justo in hac habitasse platea dictumst etiam faucibus cursus urna ut tellus nulla ut erat id mauris vulputate elementum nullam varius nulla facilisi cras non velit nec nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla eget eros elementum pellentesque quisque porta volutpat erat quisque erat eros viverra eget congue eget semper rutrum nulla nunc purus phasellus in felis donec semper sapien a libero nam dui proin leo odio', 'S1 2016', 'in porttitor pede justo eu massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea dictumst morbi vestibulum velit id pretium iaculis diam erat fermentum justo nec condimentum neque sapien placerat ante nulla justo aliquam quis turpis eget', 'ut erat curabitur gravida nisi at nibh in hac habitasse platea dictumst aliquam augue quam sollicitudin vitae consectetuer eget rutrum at lorem integer tincidunt ante vel ipsum praesent blandit', NULL),
(24, 11, 'The Effects of Different Soil Types on Plant Growth', 'quisque id justo sit amet sapien dignissim vestibulum vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae nulla dapibus dolor vel est donec odio justo sollicitudin ut', 'S1 2003', 'mi sit amet lobortis sapien sapien non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum nulla tellus in sagittis dui vel nisl duis ac nibh fusce lacus purus aliquet at feugiat non pretium quis', 'non velit nec nisi vulputate nonummy maecenas tincidunt lacus at', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` char(128) NOT NULL,
  `password` char(128) NOT NULL,
  `fname` char(50) NOT NULL,
  `lname` char(50) NOT NULL,
  `email` char(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fname`, `lname`, `email`) VALUES
(1, 'naltman', '71f87300d9434fef0e360ce9a4fc2b1284810541f4c837192d9ae49328ff78ae', 'Nathan', 'Altman', 'nathan.recruiter@example.com'),
(5, 'mfletcher', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Mackenzie', 'Fletcher', 'mackenziefletcher@hotmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients_organisations`
--
ALTER TABLE `clients_organisations`
  ADD PRIMARY KEY (`client_id`,`organisation_id`),
  ADD KEY `organisation_id` (`organisation_id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_ClientContact` (`client_id`);

--
-- Indexes for table `organisations`
--
ALTER TABLE `organisations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_ClientProject` (`client_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `organisations`
--
ALTER TABLE `organisations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clients_organisations`
--
ALTER TABLE `clients_organisations`
  ADD CONSTRAINT `clients_organisations_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `clients_organisations_ibfk_2` FOREIGN KEY (`organisation_id`) REFERENCES `organisations` (`id`);

--
-- Constraints for table `contact`
--
ALTER TABLE `contact`
  ADD CONSTRAINT `FK_ClientContact` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `FK_ClientProject` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
