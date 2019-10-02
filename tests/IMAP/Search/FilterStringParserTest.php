<?php declare(strict_types=1);

/**
 * @copyright 2019 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @author 2019 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\Mail\Tests\IMAP\Search;

use ChristophWurst\Nextcloud\Testing\TestCase;
use OCA\Mail\Service\Search\FilterStringParser;
use OCA\Mail\Service\Search\SearchQuery;

class FilterStringParserTest extends TestCase {

	private function search($filter) {
		$helper = new FilterStringParser();
		return $helper->parse($filter);
	}

	public function testSearchEmpty() {
		$this->assertEmpty($this->search('')->getTextTokens());
	}

	public function testSearchTest() {
		$this->assertEquals(['dummy', 'text'], $this->search('dummy text')->getTextTokens());
	}

	public function testSearchUnread() {
		$this->assertEquals(['SEEN' => false], $this->search('is:unread')->getFlags());
	}

	public function testSearchNotAnswered() {
		$this->assertEquals(['ANSWERED' => false], $this->search('not:answered')->getFlags());
	}

	public function testSearchFrom() {
		$this->assertEquals(['somebody@example.com'], $this->search('from:somebody@example.com')->getFrom());
	}

	public function testSearchMixed() {
		$expected = new SearchQuery();
		$expected->addFlag('SEEN', false);
		$expected->addTextToken('nextcloud');
		$expected->addFrom('somebody@example.com');

		$this->assertEquals($expected, $this->search('from:somebody@example.com is:unread nextcloud'));
	}
}
