<?php
/**
 * @version		$Id: JDatabasePostgreSQLTest.php 20196 2011-01-09 02:40:25Z Gabriele $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_PLATFORM.'/joomla/log/log.php';
require_once JPATH_PLATFORM.'/joomla/database/database.php';
require_once JPATH_PLATFORM.'/joomla/database/database/postgresql.php';
require_once JPATH_PLATFORM.'/joomla/database/databasequery.php';
require_once JPATH_PLATFORM.'/joomla/database/database/postgresqlquery.php';

/**
 * Test class for JDatabasePostgreSQL.
 */
class JDatabasePostgreSQLTest extends JoomlaDatabaseTestCase
{
	/**
	 * @var  JDatabasePostgreSQL
	 */
	protected $object;

	/**
	 * Data for the testEscape test.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	public function dataTestEscape()
	{
		return array(
			array("'%_abc123", false, '\\\'%_abc123'),
			array("'%_abc123", true, '\\\'\\%\_abc123'),
		);
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  xml dataset
	 *
	 * @since   11.1
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__).'/TestStubs/database.xml');
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function setUp()
	{
		@include_once JPATH_TESTS . '/config.php';
		if (class_exists('JPostgreSQLTestConfig')) {
			$config = new JPostgreSQLTestConfig;
		} else {
			$this->markTestSkipped('There is no PostgreSQL test config file present.');
		}
		$this->object = JDatabase::getInstance(
			array(
				'driver' => $config->dbtype,
				'database' => $config->db,
				'host' => $config->host,
				'user' => $config->user,
				'password' => $config->password
			)
		);

		parent::setUp();
	}

	/**
	 * @todo Implement test__destruct().
	 */
	public function test__destruct()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testConnected().
	 */
	public function testConnected()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the JDatabasePostgreSQL escape method.
	 *
	 * @param   string  $text   The string to be escaped.
	 * @param   bool    $extra  Optional parameter to provide extra escaping.
	 *
	 * @return  void
	 *
	 * @since   11.1
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
	 * @todo Implement testExplain().
	 */
	public function testExplain()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test getAffectedRows method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testGetAffectedRows()
	{
		$query = $this->object->getQuery(true);
		$query->delete();
		$query->from('jos_dbtest');
		$this->object->setQuery($query);

		$result = $this->object->query();

		$this->assertThat(
			$this->object->getAffectedRows(),
			$this->equalTo(4),
			__LINE__
		);
	}

	/**
	 * @todo Implement testGetCollation().
	 */
	public function testGetCollation()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetEscaped().
	 */
	public function testGetEscaped()
	{
		// TODO Check that this method proxies to "escape".

		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetNumRows().
	 */
	public function testGetNumRows()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetTableCreate().
	 */
	public function testGetTableCreate()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetTableFields().
	 */
	public function testGetTableFields()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetTableList().
	 */
	public function testGetTableList()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testGetVersion().
	 */
	public function testGetVersion()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testHasUTF().
	 */
	public function testHasUTF()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testInsertid().
	 */
	public function testInsertid()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testInsertObject().
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
	 * @since   11.1
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
	 * @since   11.1
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
			$this->equalTo(array(
				array('title' => 'Testing'),
				array('title' => 'Testing2'),
				array('title' => 'Testing3'),
				array('title' => 'Testing4'),
			)),
			__LINE__
		);
	}

	/**
	 * Test loadColumn method
	 *
	 * @return  void
	 *
	 * @since   11.1
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
	 * @todo Implement testLoadNextObject().
	 */
	public function testLoadNextObject()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testLoadNextRow().
	 */
	public function testLoadNextRow()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test loadObject method
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testLoadObject()
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$query->where('description='.$this->object->quote('three'));
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
	 * @since   11.1
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
	 * @since   11.1
	 */
	public function testLoadResult()
	{
		$query = $this->object->getQuery(true);
		$query->select('id');
		$query->from('jos_dbtest');
		$query->where('title='.$this->object->quote('Testing2'));

		$this->object->setQuery($query);
		$result = $this->object->loadResult();

		$this->assertThat(
			$result,
			$this->equalTo(2),
			__LINE__
		);

	}

	/**
	 * @todo Implement testLoadResultArray().
	 */
	public function testLoadResultArray()
	{
		// TODO Check that this method proxies to "loadColumn".

		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test loadRow method
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testLoadRow()
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$query->where('description='.$this->object->quote('three'));
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
	 * @since   11.1
	 */
	public function testLoadRowList()
	{
		$query = $this->object->getQuery(true);
		$query->select('*');
		$query->from('jos_dbtest');
		$query->where('description='.$this->object->quote('one'));
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
	 * Test the JDatabasePostgreSQL::query() method
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testQuery()
	{
		$this->object->setQuery("REPLACE INTO `jos_dbtest` SET `id` = 5, `title` = 'testTitle'");

		$this->assertThat(
			$this->object->query(),
			$this->isTrue(),
			__LINE__
		);

		$this->assertThat(
			$this->object->insertid(),
			$this->equalTo(5),
			__LINE__
		);

	}

	/**
	 * @todo Implement testQueryBatch().
	 */
	public function testQueryBatch()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testSelect().
	 */
	public function testSelect()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @todo Implement testSetUTF().
	 */
	public function testSetUTF()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test Test method - there really isn't a lot to test here, but
	 * this is present for the sake of completeness
	 */
	public function testTest()
	{
		$this->assertThat(
			JDatabasePostgreSQL::test(),
			$this->isTrue(),
			__LINE__
		);
	}

	/**
	 * @todo Implement testUpdateObject().
	 */
	public function testUpdateObject()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}
}