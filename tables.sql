--
-- Database: `prestashop`
--

-- --------------------------------------------------------

--
-- Table structure for table `ps_categoriestoseller`
--

CREATE TABLE `ps_categoriestoseller` (
  `id_stc` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table `ps_categoriestoseller_categories`
--

CREATE TABLE `ps_categoriestoseller_categories` (
  `id_stc_c` int(11) NOT NULL,
  `orderchar` varchar(1) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Indexes for dumped tables
--

--
-- Indexes for table `ps_categoriestoseller`
--
ALTER TABLE `ps_categoriestoseller`
  ADD KEY `id_stc` (`id_stc`);

--
-- Indexes for table `ps_categoriestoseller_categories`
--
ALTER TABLE `ps_categoriestoseller_categories`
  ADD PRIMARY KEY (`id_stc_c`) USING BTREE;


