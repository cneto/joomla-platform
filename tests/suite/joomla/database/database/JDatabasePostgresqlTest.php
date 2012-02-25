<?php
/**
 * @version    $Id: JDatabasePostgresqlTest.php gpongelli $
 * @package    Joomla.UnitTest
 * 
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license    GNU General Public License
 */

require_once JPATH_PLATFORM . '/joomla/log/log.php';
require_once JPATH_PLATFORM . '/joomla/database/driver/postgresql.php';
require_once JPATH_PLATFORM . '/joomla/database/query/postgresql.php';
require_once JPATH_TESTS . '/includes/JoomlaDatabasePostgresqlTestCase.php';

/**
 * Test class for JDatabasePostgresql.
 * 
 * @package     Joomla.UnitTest
 * @subpackage  Database
 * 
 * @since       11.3
 */
class JDatabasePostgresqlTest extends JoomlaDatabasePostgresqlTestCase
{
	/**
	 * @var  JDatabasePostgresql
	 */
	protected $object;

	/**
	 * Data for the testEscape test.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function dataTestEscape()
	{
		return array(
			/* ' will be escaped and become '' */
			array("'%_abc123", false, '\'\'%_abc123'),
			array("'%_abc123", true, '\'\'\%\_abc123'),
			/* ' and \ will be escaped: the first become '', the latter \\ */
			array("\'%_abc123", false, '\\\\\'\'%_abc123'),
			array("\'%_abc123", true, '\\\\\'\'\%\_abc123'),
		);
	}

	/**
	 * Data for the testGetEscaped test, proxies of escape, so same data test.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function dataTestGetEscaped()
	{
		return array(
			/* ' will be escaped and become '' */
			array("'%_abc123", false),
			array("'%_abc123", true),
			/* ' and \ will be escaped: the first become '', the latter \\ */
			array("\'%_abc123", false),
			array("\'%_abc123", true),
		);
	}

	/**
	 * Data for the testTransactionRollback test.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function dataTestTransactionRollback()
	{
		return array(
			array ( null , 0 ),
			array ( 'transactionSavepoint' , 1 )
		);
	}

	/**
	 * Data for the getCreateDbQuery test.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function dataGetCreateDbQuery()
	{
		$obj = new stdClass;
		$obj->db_user = 'testName';
		$obj->db_name = 'testDb';

		return array(
				array( $obj, false ),
				array( $obj, true )
			);
	}

	/**
	 * Data for the TestReplacePrefix test.
	 *
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function dataTestReplacePrefix()
	{
		return array(
			/* no prefix inside, no change */
			array('SELECT * FROM table', '#__', 'SELECT * FROM table'),
			/* the prefix inside double quote has to be changed */
			array('SELECT * FROM "#__table"', '#__', 'SELECT * FROM "jos_table"'),
			/* the prefix inside single quote hasn't to be changed */
			array('SELECT * FROM \'#__table\'', '#__', 'SELECT * FROM \'#__table\''),
			/* mixed quote case */
			array('SELECT * FROM \'#__table\', "#__tableSecond"', '#__', 'SELECT * FROM \'#__table\', "jos_tableSecond"'),
			/* the prefix used in sequence name (single quote) has to be changed */
			array('SELECT * FROM currval(\'#__table_id_seq\'::regclass)', '#__', 'SELECT * FROM currval(\'jos_table_id_seq\'::regclass)'),
			/* using another prefix */
			array('SELECT * FROM "#!-_table"', '#!-_', 'SELECT * FROM "jos_table"'),
		);
	}

	/**
	 * Data for testQuoteName test.
	 * 
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function dataTestQuoteName()
	{
		return array(
			/* no dot inside var */
			array('jos_dbtest', null, '"jos_dbtest"'),
			/* a dot inside var */
			array('public.jos_dbtest', null, '"public"."jos_dbtest"'),
			/* two dot inside var */
			array('joomla_ut.public.jos_dbtest', null, '"joomla_ut"."public"."jos_dbtest"'),
			/* using an array */
			array(array('joomla_ut','dbtest'), null, array('"joomla_ut"', '"dbtest"')),
			/* using an array with dotted name */
			array(array('joomla_ut.dbtest','public.dbtest'), null, array('"joomla_ut"."dbtest"','"public"."dbtest"')),
			/* using an array with two dot in name */
			array(array('joomla_ut.public.dbtest','public.dbtest.col'), null, array('"joomla_ut"."public"."dbtest"','"public"."dbtest"."col"')),

			/*** same tests with AS part ***/
			array('jos_dbtest', 'test', '"jos_dbtest" AS "test"'),
			array('public.jos_dbtest', 'tst', '"public"."jos_dbtest" AS "tst"'),
			array('joomla_ut.public.jos_dbtest', 'tst', '"joomla_ut"."public"."jos_dbtest" AS "tst"'),
			array(
				array('joomla_ut','dbtest'),
				array('j_ut', 'tst'),
				array('"joomla_ut" AS "j_ut"', '"dbtest" AS "tst"')
			),
			array(
				array('joomla_ut.dbtest','public.dbtest'),
				array('j_ut_db', 'pub_tst'),
				array('"joomla_ut"."dbtest" AS "j_ut_db"','"public"."dbtest" AS "pub_tst"')
			),
			array(
				array('joomla_ut.public.dbtest','public.dbtest.col'),
				array('j_ut_p_db', 'pub_tst_col'),
				array('"joomla_ut"."public"."dbtest" AS "j_ut_p_db"','"public"."dbtest"."col" AS "pub_tst_col"')
			),
			/* last test but with one null inside array */
			array(
				array('joomla_ut.public.dbtest','public.dbtest.col'),
				array('j_ut_p_db', null),
				array('"joomla_ut"."public"."dbtest" AS "j_ut_p_db"','"public"."dbtest"."col"')
			),
		);
	}

	/**
	 * Data for testLoadNextObject test.
	 * 
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function dataTestLoadNextObject()
	{
		$objCompOne = new stdClass;
		$objCompOne->id = 1;
		$objCompOne->title = 'Testing';
		$objCompOne->start_date = '1980-04-18 00:00:00';
		$objCompOne->description = 'one';

		$objCompTwo = new stdClass;
		$objCompTwo->id = 2;
		$objCompTwo->title = 'Testing2';
		$objCompTwo->start_date = '1980-04-18 00:00:00';
		$objCompTwo->description = 'one';

		$objCompThree = new stdClass;
		$objCompThree->id = 3;
		$objCompThree->title = 'Testing3';
		$objCompThree->start_date = '1980-04-18 00:00:00';
		$objCompThree->description = 'three';

		$objCompFour = new stdClass;
		$objCompFour->id = 4;
		$objCompFour->title = 'Testing4';
		$objCompFour->start_date = '1980-04-18 00:00:00';
		$objCompFour->description = 'four';

		return array(
			array(array($objCompOne, $objCompTwo, $objCompThree, $objCompFour))
		);
	}

	/**
	 * Data for testLoadNextRow test.
	 * 
	 * @return  array
	 *
	 * @since   11.3
	 */
	public function dataTestLoadNextRow()
	{
		return array(
			array(
				array(
					array(1, 'Testing', '1980-04-18 00:00:00', 'one'),
					array(2, 'Testing2', '1980-04-18 00:00:00', 'one'),
					array(3, 'Testing3', '1980-04-18 00:00:00', 'three'),
					array(4, 'Testing4', '1980-04-18 00:00:00', 'four')
				)
			)
		);
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  xml dataset
	 *
	 * @since   11.3
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/stubs/database.xml');
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	protected function setUp()
	{
		@include_once JPATH_TESTS . '/config_postgresql.php';
		if (class_exists('JPostgresqlTestConfig'))
		{
			$config = new JPostgresqlTestConfig;
		}
		else
		{
			$this->markTestSkipped('There is no Postgresql test config file present.');
		}

		try
		{
			$this->object = JDatabase::getInstance(
				array(
					'driver' => $config->dbtype,
					'database' => $config->db,
					'host' => $config->host,
					'user' => $config->user,
					'password' => $config->password
				)
			);
		}
		catch (RuntimeException $jdbException)
		{
			$this->markTestSkipped('Postgresql database not present or wrong configuration.');
		}

		parent::setUp();
	}

	/**
	 * Tear down function used to clean inserted data or insert back deleted data.
	 * 
	 * @return   void
	 */
	protected function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * Test destruct
	 * 
	 * @todo Implement test__destruct().
	 * 
	 * @return   void
	 */
	public function test__destruct()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Check if connected() method returns true.
	 * 
	 * @return   void
	 */
	public function testConnected()
	{
		$this->assertThat(
			$this->object->connected(),
			$this->equalTo(true),
			'Not connected to database'
		);
	}

	/**
	 * Tests the JDatabasePostgresql escape method.
	 *
	 * @param   string  $text    The string to be escaped.
	 * @param   bool    $extra   Optional parameter to provide extra escaping.
	 * @param   string  $result  Correct string escaped
	 *
	 * @return  void
	 *
	 * @since   11.3
	 * @dataProvider  dataTestEscape
	 */
	public function testEscape($text, $extra, $result)
	{
		$this->assertThat(
			$this->object->escape($text, $extra),
			$this->equalTo($result),
			'The string was not escaped properly'
		);
	}

	/**
	 * Test getAffectedRows method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testGetAffectedRows()
	{
		$query = $this->object->getQuery(true);
		$query->delete();
		$query->from('jos_dbtest');
		$this->object->setQuery($query);

		$result = $this->object->execute();

		$this->assertThat(
			$this->object->getAffectedRows(),
			$this->equalTo(4),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgresql getCollation method.
	 * 
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testGetCollation()
	{
		$this->assertContains(
			'UTF-8',
			$this->object->getCollation(),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgresql getNumRows method.
	 * 
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testGetNumRows()
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$query->where('description=' . $this->object->quote('one'));
		$this->object->setQuery($query);

		$res = $this->object->execute();

		$this->assertThat(
			$this->object->getNumRows($res),
			$this->equalTo(2),
			__LINE__
		);
	}

	/**
	 * Test getTableCreate function
	 * 
	 * @todo Implement testGetTableCreate().
	 * 
	 * @return   void
	 */
	public function testGetTableCreate()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test getTableColumns function.
	 * 
	 * @return   void
	 */
	public function testGetTableColumns()
	{
		$tableCol = array(
						'id' => 'integer',
						'title' => 'character varying',
						'start_date' => 'timestamp without time zone',
						'description' => 'text'
					);

		$this->assertThat(
			$this->object->getTableColumns('jos_dbtest'),
			$this->equalTo($tableCol),
			__LINE__
		);

		/* not only type field */
		$id = new stdClass;
		$id->column_name = 'id';
		$id->type = 'integer';
		$id->null = 'NO';
		$id->default = 'nextval(\'jos_dbtest_id_seq\'::regclass)';
		$id->comments = '';

		$title = new stdClass;
		$title->column_name = 'title';
		$title->type = 'character varying(50)';
		$title->null = 'NO';
		$title->default = null;
		$title->comments = '';

		$start_date = new stdClass;
		$start_date->column_name = 'start_date';
		$start_date->type = 'timestamp without time zone';
		$start_date->null = 'NO';
		$start_date->default = null;
		$start_date->comments = '';

		$description = new stdClass;
		$description->column_name = 'description';
		$description->type = 'text';
		$description->null = 'NO';
		$description->default = null;
		$description->comments = '';

		$this->assertThat(
			$this->object->getTableColumns('jos_dbtest', false),
			$this->equalTo(array('id' => $id, 'title' => $title, 'start_date' => $start_date, 'description' => $description)),
			__LINE__
		);
	}

	/**
	 * Test getTableKeys function.
	 * 
	 * @return   void
	 */
	public function testGetTableKeys()
	{
		$pkey = new stdClass;
		$pkey->idxName = 'jos_assets_pkey';
		$pkey->isPrimary = 't';
		$pkey->isUnique = 't';
		$pkey->Query = 'ALTER TABLE jos_assets ADD PRIMARY KEY (id)';

		$asset = new stdClass;
		$asset->idxName = 'idx_asset_name';
		$asset->isPrimary = 'f';
		$asset->isUnique = 't';
		$asset->Query = 'CREATE UNIQUE INDEX idx_asset_name ON jos_assets USING btree (name)';

		$lftrgt = new stdClass;
		$lftrgt->idxName = 'jos_assets_idx_lft_rgt';
		$lftrgt->isPrimary = 'f';
		$lftrgt->isUnique = 'f';
		$lftrgt->Query = 'CREATE INDEX jos_assets_idx_lft_rgt ON jos_assets USING btree (lft, rgt)';

		$id = new stdClass;
		$id->idxName = 'jos_assets_idx_parent_id';
		$id->isPrimary = 'f';
		$id->isUnique = 'f';
		$id->Query = 'CREATE INDEX jos_assets_idx_parent_id ON jos_assets USING btree (parent_id)';

		$this->assertThat(
			$this->object->getTableKeys('jos_assets'),
			$this->equalTo(array($pkey, $id, $lftrgt, $asset)),
			__LINE__
		);
	}

	/**
	 * Test getTableSequences function.
	 * 
	 * @return   void
	 */
	public function testGetTableSequences()
	{
		$seq = new stdClass;
		$seq->sequence = 'jos_dbtest_id_seq';
		$seq->schema = 'public';
		$seq->table = 'jos_dbtest';
		$seq->column = 'id';
		$seq->data_type = 'bigint';
		$seq->start_value = '1';
		$seq->minimum_value = '1';
		$seq->maximum_value = '9223372036854775807';
		$seq->increment = '1';
		$seq->cycle_option = 'NO';

		$this->assertThat(
			$this->object->getTableSequences('jos_dbtest'),
			$this->equalTo(array($seq)),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgresql getTableList method.
	 * 
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testGetTableList()
	{
		$expected = array(
					"0" => "jos_assets",			"1" => "jos_categories",	"2" => "jos_content" ,
					"3" => "jos_core_log_searches",	"4" => "jos_dbtest",		"5" => "jos_extensions",
					"6" => "jos_languages",			"7" => "jos_log_entries",	"8" => "jos_menu",
					"9" => "jos_menu_types",		"10" => "jos_modules",		"11" => "jos_modules_menu",
					"12" => "jos_schemas",			"13" => "jos_session",		"14" => "jos_update_categories",
					"15" => "jos_update_sites",		"16" => "jos_update_sites_extensions",		"17" => "jos_updates",
					"18" => "jos_user_profiles",	"19" => "jos_user_usergroup_map",			"20" => "jos_usergroups",
					"21" => "jos_users",			"22" => "jos_viewlevels");

		$result = $this->object->getTableList();

		// Assert array size
		$this->assertThat(
			count($result),
			$this->equalTo(count($expected)),
			__LINE__
		);

		// Clear found element to check if all elements are present in any order
		foreach ($result as $k => $v)
		{
			if (in_array($v, $expected))
			{
				// Ok case, value found so set value to zero
				$result[$k] = '0';
			}
			else
			{
				// Error case, value NOT found so set value to one
				$result[$k] = '1';
			}
		}

		// If there's a one it will return true and test fails
		$this->assertThat(
			in_array('1', $result),
			$this->equalTo(false),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgresql getVersion method.
	 * 
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testGetVersion()
	{
		$versionRow = $this->object->setQuery('SELECT version();')->loadRow();
		$versionArray = explode(' ', $versionRow[0]);

		$this->assertGreaterThanOrEqual(
			$versionArray[1],
			$this->object->getVersion(),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgresql insertId method.
	 * 
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testInsertid()
	{
		$this->object->setQuery('TRUNCATE TABLE "jos_dbtest"');
		$result = $this->object->execute();

		/* increment the sequence automatically with INSERT INTO,
		 * first insert to have a common starting point */
		$query = $this->object->getQuery(true);
		$query->insert('jos_dbtest')
				->columns('title,start_date,description')
				->values("'testTitle','1970-01-01','testDescription'");
		$this->object->setQuery($query);
		$this->object->execute();

		/* get the current sequence value */
		$actualVal = $this->object->getQuery(true);
		$actualVal->select("currval('jos_dbtest_id_seq'::regclass)");
		$this->object->setQuery($actualVal);
		$idActualVal = $this->object->loadRow();

		/* insert again, then call insertid() */
		$secondInsertQuery = $this->object->getQuery(true);
		$secondInsertQuery->insert('jos_dbtest')
					->columns('title,start_date,description')
					->values("'testTitle2nd', '1971-01-01', 'testDescription2nd'");
		$this->object->setQuery($secondInsertQuery);
		$this->object->execute();

		/* get insertid of last INSERT INTO */
		$insertId = $this->object->insertid();

		/* check if first sequence val +1 is equal to last sequence val */
		$this->assertThat(
			$insertId,
			$this->equalTo($idActualVal[0] + 1),
			__LINE__
		);
	}

	/**
	 * Test insertObject function
	 * 
	 * @todo Implement testInsertObject().
	 * 
	 * @return   void
	 */
	public function testInsertObject()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test loadAssoc method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testLoadAssoc()
	{
		$query = $this->object->getQuery(true);
		$query->select('title');
		$query->from('jos_dbtest');
		$this->object->setQuery($query);
		$result = $this->object->loadAssoc();

		$this->assertThat(
			$result,
			$this->equalTo(array('title' => 'Testing')),
			__LINE__
		);
	}

	/**
	 * Test loadAssocList method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testLoadAssocList()
	{
		$query = $this->object->getQuery(true);
		$query->select('title');
		$query->from('jos_dbtest');
		$this->object->setQuery($query);
		$result = $this->object->loadAssocList();

		$this->assertThat(
			$result,
			$this->equalTo(
				array(
					array('title' => 'Testing'),
					array('title' => 'Testing2'),
					array('title' => 'Testing3'),
					array('title' => 'Testing4'),
				)
			),
			__LINE__
		);
	}

	/**
	 * Test loadColumn method
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testLoadColumn()
	{
		$query = $this->object->getQuery(true);
		$query->select('title');
		$query->from('jos_dbtest');
		$this->object->setQuery($query);
		$result = $this->object->loadColumn();

		$this->assertThat(
			$result,
			$this->equalTo(array('Testing', 'Testing2', 'Testing3', 'Testing4')),
			__LINE__
		);
	}

	/**
	 * Test loadNextObject function
	 * 
	 * @param   array  $objArr  Array of expected objects
	 * 
	 * @return   void
	 * 
	 * @dataProvider dataTestLoadNextObject
	 */
	public function testLoadNextObject($objArr)
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$this->object->setQuery($query);

		$this->assertThat(
			$this->object->loadNextObject(),
			$this->equalTo($objArr[0]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextObject(),
			$this->equalTo($objArr[1]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextObject(),
			$this->equalTo($objArr[2]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextObject(),
			$this->equalTo($objArr[3]),
			__LINE__
		);

		/* last call to free cursor, asserting that returns false */
		$this->assertFalse(
			$this->object->loadNextObject()
		);
	}

	/**
	 * Test loadNextObject function with preceding loadObject call
	 * 
	 * @param   array  $objArr  Array of expected objects
	 * 
	 * @return   void
	 * 
	 * @dataProvider dataTestLoadNextObject
	 */
	public function testLoadNextObject_plusLoad($objArr)
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$this->object->setQuery($query);

		$this->object->loadObject();

		$this->assertThat(
			$this->object->loadNextObject(),
			$this->equalTo($objArr[0]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextObject(),
			$this->equalTo($objArr[1]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextObject(),
			$this->equalTo($objArr[2]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextObject(),
			$this->equalTo($objArr[3]),
			__LINE__
		);

		/* last call to free cursor, asserting that returns false */
		$this->assertFalse(
			$this->object->loadNextObject()
		);
	}

	/**
	 * Test loadNextObject function with preceding query call
	 * 
	 * @param   array  $objArr  Array of expected objects
	 * 
	 * @return   void
	 * 
	 * @dataProvider dataTestLoadNextObject
	 */
	public function testLoadNextObject_plusQuery($objArr)
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$this->object->setQuery($query);

		$this->object->execute();

		$this->assertThat(
			$this->object->loadNextObject(),
			$this->equalTo($objArr[0]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextObject(),
			$this->equalTo($objArr[1]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextObject(),
			$this->equalTo($objArr[2]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextObject(),
			$this->equalTo($objArr[3]),
			__LINE__
		);

		/* last call to free cursor, asserting that returns false */
		$this->assertFalse(
			$this->object->loadNextObject()
		);
	}

	/**
	 * Test loadNextRow function
	 * 
	 * @param   array  $rowArr  Array of expected arrays
	 * 
	 * @return   void
	 * 
	 * @dataProvider dataTestLoadNextRow
	 */
	public function testLoadNextRow($rowArr)
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$this->object->setQuery($query);

		$this->assertThat(
			$this->object->loadNextRow(),
			$this->equalTo($rowArr[0]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextRow(),
			$this->equalTo($rowArr[1]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextRow(),
			$this->equalTo($rowArr[2]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextRow(),
			$this->equalTo($rowArr[3]),
			__LINE__
		);

		/* last call to free cursor, asserting that returns false */
		$this->assertFalse(
			$this->object->loadNextRow()
		);
	}

	/**
	 * Test loadNextRow function with preceding query call
	 * 
	 * @param   array  $rowArr  Array of expected arrays
	 * 
	 * @return   void
	 * 
	 * @dataProvider dataTestLoadNextRow
	 */
	public function testLoadNextRow_plusQuery($rowArr)
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$this->object->setQuery($query);

		$this->object->execute();

		$this->assertThat(
			$this->object->loadNextRow(),
			$this->equalTo($rowArr[0]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextRow(),
			$this->equalTo($rowArr[1]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextRow(),
			$this->equalTo($rowArr[2]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextRow(),
			$this->equalTo($rowArr[3]),
			__LINE__
		);

		/* last call to free cursor, asserting that returns false */
		$this->assertFalse(
			$this->object->loadNextRow()
		);
	}

	/**
	 * Test loadNextRow function with preceding loadRow call
	 * 
	 * @param   array  $rowArr  Array of expected arrays
	 * 
	 * @return   void
	 * 
	 * @dataProvider dataTestLoadNextRow
	 */
	public function testLoadNextRow_plusLoad($rowArr)
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$this->object->setQuery($query);

		$this->object->loadRow();

		$this->assertThat(
			$this->object->loadNextRow(),
			$this->equalTo($rowArr[0]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextRow(),
			$this->equalTo($rowArr[1]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextRow(),
			$this->equalTo($rowArr[2]),
			__LINE__
		);

		$this->assertThat(
			$this->object->loadNextRow(),
			$this->equalTo($rowArr[3]),
			__LINE__
		);

		/* last call to free cursor, asserting that returns false */
		$this->assertFalse(
			$this->object->loadNextRow()
		);
	}

	/**
	 * Test loadObject method
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testLoadObject()
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$query->where('description=' . $this->object->quote('three'));
		$this->object->setQuery($query);
		$result = $this->object->loadObject();

		$objCompare = new stdClass;
		$objCompare->id = 3;
		$objCompare->title = 'Testing3';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'three';

		$this->assertThat(
			$result,
			$this->equalTo($objCompare),
			__LINE__
		);
	}

	/**
	 * Test loadObjectList method
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testLoadObjectList()
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$query->order('id');
		$this->object->setQuery($query);
		$result = $this->object->loadObjectList();

		$expected = array();

		$objCompare = new stdClass;
		$objCompare->id = 1;
		$objCompare->title = 'Testing';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'one';

		$expected[] = clone $objCompare;

		$objCompare = new stdClass;
		$objCompare->id = 2;
		$objCompare->title = 'Testing2';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'one';

		$expected[] = clone $objCompare;

		$objCompare = new stdClass;
		$objCompare->id = 3;
		$objCompare->title = 'Testing3';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'three';

		$expected[] = clone $objCompare;

		$objCompare = new stdClass;
		$objCompare->id = 4;
		$objCompare->title = 'Testing4';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'four';

		$expected[] = clone $objCompare;

		$this->assertThat(
			$result,
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * Test loadResult method
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testLoadResult()
	{
		$query = $this->object->getQuery(true);
		$query->select('id');
		$query->from('jos_dbtest');
		$query->where('title=' . $this->object->quote('Testing2'));

		$this->object->setQuery($query);
		$result = $this->object->loadResult();

		$this->assertThat(
			$result,
			$this->equalTo(2),
			__LINE__
		);
	}

	/**
	 * Test loadRow method
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testLoadRow()
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$query->where('description=' . $this->object->quote('three'));
		$this->object->setQuery($query);
		$result = $this->object->loadRow();

		$expected = array(3, 'Testing3', '1980-04-18 00:00:00', 'three');

		$this->assertThat(
			$result,
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * Test loadRowList method
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testLoadRowList()
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$query->where('description=' . $this->object->quote('one'));
		$this->object->setQuery($query);
		$result = $this->object->loadRowList();

		$expected = array(
			array(1, 'Testing', '1980-04-18 00:00:00', 'one'),
			array(2, 'Testing2', '1980-04-18 00:00:00', 'one')
		);

		$this->assertThat(
			$result,
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * Test the JDatabasePostgresql::query() method
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testQuery()
	{
		/* REPLACE is not present in PostgreSQL */
		$query = $this->object->getQuery(true);
		$query->delete();
		$query->from('jos_dbtest')->where('id=5');
		$this->object->setQuery($query);
		$result = $this->object->execute();

		$query = $this->object->getQuery(true);
		$query->insert('jos_dbtest')
				->columns('id,title,start_date, description')
				->values("5, 'testTitle','1970-01-01','testDescription'")
				->returning('id');

		$this->object->setQuery($query);
		$arr = $this->object->loadResult();

		$this->assertThat(
			$arr,
			$this->equalTo(5),
			__LINE__
		);
	}

	/**
	 * Test quoteName function, with and without dot notation.
	 * 
	 * @param   string  $quoteMe   String to be quoted
	 * @param   string  $asPart    String used for AS query part
	 * @param   string  $expected  Expected string
	 * 
	 * @return void
	 * 
	 * @since 11.3
	 * @dataProvider dataTestQuoteName
	 */
	public function testQuoteName( $quoteMe, $asPart, $expected )
	{
		$this->assertThat(
			$this->object->quoteName($quoteMe, $asPart),
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgresql select method.
	 * 
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testSelect()
	{
		/* it's not possible to select a database, already done during connection, return true */
		$this->assertThat(
			$this->object->select('database'),
			$this->isTrue(),
			__LINE__
		);
	}

	/**
	 * Test setUTF function
	 * 
	 * @return   void
	 */
	public function testSetUTF()
	{
		$this->assertThat(
			$this->object->setUTF(),
			$this->equalTo(0),
			__LINE__
		);
	}

	/**
	 * Test Test method - there really isn't a lot to test here, but
	 * this is present for the sake of completeness
	 * 
	 * @return   void
	 */
	public function testTest()
	{
		$this->assertThat(
			JDatabaseDriverPostgresql::test(),
			$this->isTrue(),
			__LINE__
		);
	}

	/**
	 * Test updateObject function.
	 * 
	 * @todo Implement testUpdateObject().
	 * 
	 * @return  void
	 */
	public function testUpdateObject()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the JDatabasePostgresql transactionCommit method.
	 * 
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testTransactionCommit()
	{
		$this->object->transactionStart();
		$queryIns = $this->object->getQuery(true);
		$queryIns->insert('jos_dbtest')
					->columns('id,title,start_date,description')
					->values("6, 'testTitle','1970-01-01','testDescription'");

		$this->object->setQuery($queryIns);
		$arr = $this->object->execute();

		$this->object->transactionCommit();

		/* check if value is present */
		$queryCheck = $this->object->getQuery(true);
		$queryCheck->select('*')
					->from('jos_dbtest')
					->where('id=6');
		$this->object->setQuery($queryCheck);
		$result = $this->object->loadRow();

		$expected = array(6, 'testTitle', '1970-01-01 00:00:00','testDescription');

		$this->assertThat(
			$result,
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgresql transactionRollback method, 
	 * with and without savepoint.
	 * 
	 * @param   string  $toSavepoint  Savepoint name to rollback transaction to
	 * @param   int     $tupleCount   Number of tuple found after insertion and rollback
	 * 
	 * @return  void
	 *
	 * @since   11.3
	 * @dataProvider dataTestTransactionRollback
	 */
	public function testTransactionRollback ( $toSavepoint, $tupleCount )
	{
		$this->object->transactionStart();

		/* try to insert this tuple, inserted only when savepoint != null */
		$queryIns = $this->object->getQuery(true);
		$queryIns->insert('jos_dbtest')
					->columns('id,title,start_date,description')
					->values("7, 'testRollback','1970-01-01','testRollbackSp'");
		$this->object->setQuery($queryIns);
		$arr = $this->object->execute();

		/* create savepoint only if is passed by data provider */
		if ( !is_null($toSavepoint) )
		{
			$this->object->transactionSavepoint($toSavepoint);
		}

		/* try to insert this tuple, always rolled back */
		$queryIns = $this->object->getQuery(true);
		$queryIns->insert('jos_dbtest')
					->columns('id,title,start_date,description')
					->values("8, 'testRollback','1972-01-01','testRollbackSp'");
		$this->object->setQuery($queryIns);
		$arr = $this->object->execute();

		$this->object->transactionRollback($toSavepoint);

		/* release savepoint and commit only if a savepoint exists */
		if ( !is_null($toSavepoint) )
		{
			$this->object->releaseTransactionSavepoint($toSavepoint);
			$this->object->transactionCommit();
		}

		/* find how many rows have description='testRollbackSp' :
		 *   - 0 if a savepoint doesn't exist
		 *   - 1 if a savepoint exists
		 */
		$queryCheck = $this->object->getQuery(true);
		$queryCheck->select('*')
					->from('jos_dbtest')
					->where("description='testRollbackSp'");
		$this->object->setQuery($queryCheck);
		$result = $this->object->loadRowList();

		$this->assertThat(
			count($result),
			$this->equalTo($tupleCount),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgresql transactionStart method.
	 * 
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testTransactionStart()
	{
		$this->object->transactionRollback();
		$this->object->transactionStart();
		$queryIns = $this->object->getQuery(true);
		$queryIns->insert('jos_dbtest')
					->columns('id,title,start_date,description')
					->values("6, 'testTitle','1970-01-01','testDescription'");

		$this->object->setQuery($queryIns);
		$arr = $this->object->execute();

		/* check if is present an exclusive lock, it means a transaction is running */
		$queryCheck = $this->object->getQuery(true);
		$queryCheck->select('*')
					->from('pg_catalog.pg_locks')
					->where('transactionid NOTNULL');
		$this->object->setQuery($queryCheck);
		$result = $this->object->loadAssocList();

		$this->assertThat(
			count($result),
			$this->equalTo(1),
			__LINE__
		);
	}

	/**
	 * Test for release of transaction savepoint, correct case is already tested inside
	 * 		testTransactionRollback, here will be tested a RELEASE SAVEPOINT of an
	 * 		inexistent savepoint that will throw and exception.
	 * 
	 * @return  void
	 * 
	 * @expectedException RuntimeException
	 */
	public function testReleaseTransactionSavepoint()
	{
		$this->object->transactionRollback();
		$this->object->transactionStart();

		/* release a nonexistent savepoint will throw an exception */
		try
		{
			$this->object->releaseTransactionSavepoint('pippo');
		}
		catch (RuntimeException $e)
		{
			$this->object->transactionRollback();
			throw $e;
		}
	}

	/**
	 * Tests the JDatabasePostgresql renameTable method.
	 * 
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function testRenameTable()
	{
		$newTableName = 'bak_jos_dbtest';

		$this->object->renameTable('jos_dbtest', $newTableName);

		/* check name change */
		$tableList = $this->object->getTableList();
		$this->assertThat(
			in_array($newTableName, $tableList),
			$this->isTrue(),
			__LINE__
		);

		/* check index change */
		$this->object->setQuery(
						'SELECT relname 
							FROM pg_class 
							WHERE oid IN ( 
								SELECT indexrelid 
								FROM pg_index, pg_class 
								WHERE pg_class.relname=\'' . $newTableName .
								'\' AND pg_class.oid=pg_index.indrelid );'
		);

		$oldIndexes = $this->object->loadColumn();
		$this->assertThat(
			$oldIndexes[0],
			$this->equalTo('bak_jos_dbtest_pkey'),
			__LINE__
		);

		/* check sequence change */
		$this->object->setQuery(
						'SELECT relname
							FROM pg_class
							WHERE relkind = \'S\'
							AND relnamespace IN (
								SELECT oid
								FROM pg_namespace
								WHERE nspname NOT LIKE \'pg_%\'
								AND nspname != \'information_schema\'
							)
							AND relname LIKE \'%' . $newTableName . '%\' ;'
		);

		$oldSequences = $this->object->loadColumn();
		$this->assertThat(
			$oldSequences[0],
			$this->equalTo('bak_jos_dbtest_id_seq'),
			__LINE__
		);

		/* restore initial state */
		$this->object->renameTable($newTableName, 'jos_dbtest');
	}

	/**
	 * Tests the JDatabasePostgresql replacePrefix method.
	 * 
	 * @param   text  $stringToReplace  The string in which replace the prefix.
	 * @param   text  $prefix           The prefix.
	 * @param   text  $expected         The string expected.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 * @dataProvider  dataTestReplacePrefix
	 */
	public function testReplacePrefix( $stringToReplace, $prefix, $expected )
	{
		$result = $this->object->replacePrefix($stringToReplace, $prefix);

		$this->assertThat(
			$result,
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * Test for creation of transaction savepoint
	 * 
	 * @todo Implement testTransactionSavepoint().
	 * 
	 * @return  void
	 */
	public function testTransactionSavepoint( /*$savepointName*/ )
	{
		$this->markTestSkipped('This command is tested inside testTransactionRollback.');
	}

	/**
	 * Tests the JDatabasePostgresql getCreateDbQuery method.
	 * 
	 * @param   JObject  $options  JObject coming from "initialise" function to pass user 
	 * 									and database name to database driver.
	 * @param   boolean  $utf      True if the database supports the UTF-8 character set.
	 * 
	 * @return  void
	 *
	 * @dataProvider dataGetCreateDbQuery
	 */
	public function testGetCreateDbQuery( $options, $utf )
	{
		$expected = 'CREATE DATABASE ' . $this->object->quoteName($options->db_name) . ' OWNER ' . $this->object->quoteName($options->db_user);

		if ( $utf )
		{
			$expected .= ' ENCODING ' . $this->object->quote('UTF-8');
		}

		$result = $this->object->getCreateDbQuery($options, $utf);

		$this->assertThat(
			$result,
			$this->equalTo($expected),
			__LINE__
		);
	}

	/**
	 * Tests the JDatabasePostgresql getAlterDbCharacterSet method. 
	 * 
	 * @return  void
	 */
	public function testGetAlterDbCharacterSet()
	{
		$expected = 'ALTER DATABASE ' . $this->object->quoteName('test') . ' SET CLIENT_ENCODING TO ' . $this->object->quote('UTF8');

		$result = $this->object->getAlterDbCharacterSet('test');

		$this->assertThat(
			$result,
			$this->equalTo($expected),
			__LINE__
		);
	}
}