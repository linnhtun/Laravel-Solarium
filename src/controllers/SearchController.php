<?php namespace Fbf\LaravelSolarium;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class SearchController extends BaseController {

	public function results(Request $request)
	{
		$results = $paginator = false;

	    if ( $request->has('term') )
        {
            $solr = new LaravelSolariumQuery(\Config::get('laravel-5-solarium.default_core'));

            $searchInput = $request->get('term');

            $searchArray = explode(' ', $searchInput);

            $searchTermsArray = array();

            foreach ( $searchArray as $term )
            {
                $searchTermArray[] = 'search_content:"'.trim($term).'"';
            }

            $searchTerm = implode(' OR ', $searchTermArray);

            $resultsPerPage = \Config::get('laravel-5-solarium.results.items_per_page');

            $results = $solr->search($searchTerm)
                ->fields(array('id', 'title', 'content', 'url'))
                ->page($request->get('page', 1), $resultsPerPage)
                ->highlight(array('content'))
                ->get();

            $highlighting = $results->getHighlighting();

            $paginator = new Paginator(
                $results->getDocuments(),
                $resultsPerPage,
                $request->get('page', 1),
                array()
            );
        }

        return response()->json(compact('results', 'paginator', 'highlighting'));
	}
}