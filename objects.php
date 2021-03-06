<?php

class Object {

	private $json;

	public function __construct(){
	}

  public function getMeQuery(){
		$query = <<<'JSON'
				query getMe {  
					me {
						_id 
						email 
						profile {  
							firstName  
							__typename
						} 
					roles 
					__typename  
					}
				}
JSON;
		/*Me es sin variables*/
		$this->json= json_encode(['query' => $query]);
	}


	public function getMyClosedOrders($code){
		$query = <<<'JSON'
			query myClosedOrders($marketCode: ID!, $page: Int) {
			  orders(marketCode: $marketCode, onlyClosed: true, limit: null, page: $page) {
			    totalCount
			    hasNextPage
			    page
			    items {
			      _id
			      sell
			      type
			      amount
			      amountToHold
			      secondaryAmount
			      filled
			      closedAt
			      secondaryFilled
			      limitPrice
			      createdAt
			      activatedAt
			      isStop
			      status
			      stopPriceUp
			      stopPriceDown
						trades{     
							_id      
							amount
							price      
							totalCost      
							date    
						} 
			      market {
			        name
			        code
			        mainCurrency {
			          code
			          format
			          longFormat
			          units
			          __typename
			        }
			        secondaryCurrency {
			          code
			          format
			          longFormat
			          units
			          __typename
			        }
			        __typename
			      }
			      __typename
			    }
			    __typename
			  }
			}
JSON;
		$variables = <<<JSON
				{ "marketCode": "{$code}", "page": 1 }
JSON;
		$this->json= json_encode(['query' => $query, 'variables' => $variables]);	
	}


	public function getMarketOrderBook($code){
		$query = <<<'JSON'
				query  getmarketOrderBook($code: ID!){ 
					marketOrderBook(marketCode: $code ,limit:1) {
						buy {
							limitPrice
						}
						sell {
							limitPrice 
						} 
						spread 
						mid
					}
				}
JSON;
		$variables = <<<JSON
				{ "code": "{$code}" }
JSON;
		$this->json= json_encode(['query' => $query, 'variables' => $variables]);
	}
	

	public function getMarketCurrentStats($code){
		$query = <<<'JSON'
		query marketCurrentStats($marketCode: ID!) {
		  market(code: $marketCode) {
		    code
		    name
		    lastTrade {
		      price
		      __typename
		    }
		    mainCurrency {
		      code
		      units
		      format
		      __typename
		    }
		    secondaryCurrency {
		      code
		      units
		      format
		      __typename
		    }
		    __typename
		  }
		  stats: marketCurrentStats(marketCode: $marketCode, aggregation: d1) {
		    close
		    volume
		    variation
		    __typename
		  }
		}
JSON;
		$variables = <<<JSON
				{ "marketCode": "{$code}" }
JSON;
		$this->json= json_encode(['query' => $query, 'variables' => $variables]);
	}


	public function __get($property) {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  public function __set($property, $value) {
    if (property_exists($this, $property)) {
      $this->$property = $value;
    }
    return $this;
  }
}

?>
