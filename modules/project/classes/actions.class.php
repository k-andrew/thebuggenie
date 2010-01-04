<?php

	/**
	 * actions for the project module
	 */
	class projectActions extends BUGSaction
	{

		/**
		 * The currently selected project
		 * 
		 * @var BUGSproject
		 * @access protected
		 * @property $selected_project
		 */

		/**
		 * Pre-execute function
		 *
		 * @param BUGSrequest 	$request
		 * @param string		$action
		 */
		public function preExecute(BUGSrequest $request, $action)
		{
			if ($project_key = $request->getParameter('project_key'))
			{
				try
				{
					$this->selected_project = BUGSproject::getByKey($project_key);
				}
				catch (Exception $e) {}
			}
			elseif ($project_id = $request->getParameter('project_id'))
			{
				try
				{
					$this->selected_project = BUGSfactory::projectLab($project_id);
				}
				catch (Exception $e) {}
			}
			if ($this->selected_project instanceof BUGSproject)
			{
				BUGScontext::setCurrentProject($this->selected_project);
				$this->project_key = $this->selected_project->getKey();
			}
			else
			{
				$this->return404(BUGScontext::getI18n()->__('This project does not exist'));
			}
		}

		/**
		 * The project dashboard
		 * 
		 * @param BUGSrequest $request
		 */
		public function runDashboard(BUGSrequest $request)
		{
			$this->forward403unless(BUGScontext::getUser()->hasPageAccess('project_dashboard', $this->selected_project->getID()) || BUGScontext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			$this->recent_activities = $this->selected_project->getRecentActivities(10);
			$this->recent_issues = $this->selected_project->getRecentIssues();
			$this->recent_features = $this->selected_project->getRecentFeatures();
			$this->recent_ideas = $this->selected_project->getRecentIdeas();
			$this->assignees = $this->selected_project->getAssignees();
		}

		/**
		 * The project planning page
		 *
		 * @param BUGSrequest $request
		 */
		public function runPlanning(BUGSrequest $request)
		{
			$this->forward403unless(BUGScontext::getUser()->hasPageAccess('project_planning', $this->selected_project->getID()) || BUGScontext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
		}

		/**
		 * The project planning page
		 *
		 * @param BUGSrequest $request
		 */
		public function runTimeline(BUGSrequest $request)
		{
			$this->forward403unless(BUGScontext::getUser()->hasPageAccess('project_timeline', $this->selected_project->getID()) || BUGScontext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			$this->recent_activities = $this->selected_project->getRecentActivities();
		}

		/**
		 * The project scrum page
		 *
		 * @param BUGSrequest $request
		 */
		public function runScrum(BUGSrequest $request)
		{
			$this->forward403unless(BUGScontext::getUser()->hasPageAccess('project_scrum', $this->selected_project->getID()) || BUGScontext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			$this->unassigned_issues = $this->selected_project->getUnassignedStories();
		}

		/**
		 * Set color on a user story
		 *
		 * @param BUGSrequest $request
		 */
		public function runScrumSetStoryDetail(BUGSrequest $request)
		{
			$this->forward403unless(BUGScontext::getUser()->hasPageAccess('project_scrum', $this->selected_project->getID()) || BUGScontext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			$issue = BUGSfactory::BUGSissueLab($request->getParameter('story_id'));
			if ($issue instanceof BUGSissue)
			{
				switch ($request->getParameter('detail'))
				{
					case 'color':
						$issue->setScrumColor($request->getParameter('color'));
						$issue->save();
						return $this->renderJSON(array('failed' => false));
						break;
					case 'estimates':
						$issue->setEstimatedPoints((int) $request->getParameter('estimated_points'));
						$issue->setEstimatedHours((int) $request->getParameter('estimated_hours'));
						$issue->save();
						$sprint_id = ($issue->getMilestone() instanceof BUGSmilestone) ? $issue->getMilestone()->getID() : 0;
						$new_sprint_points = ($sprint_id !== 0) ? $issue->getMilestone()->getPointsEstimated() : 0;
						$new_sprint_hours = ($sprint_id !== 0) ? $issue->getMilestone()->getHoursEstimated() : 0;
						return $this->renderJSON(array('failed' => false, 'points' => $issue->getEstimatedPoints(), 'hours' => $issue->getEstimatedHours(), 'sprint_id' => $sprint_id, 'new_estimated_points' => $new_sprint_points, 'new_estimated_hours' => $new_sprint_hours));
						break;
				}
			}
			return $this->renderJSON(array('failed' => true, 'error' => BUGScontext::getI18n()->__('Invalid user story')));
		}
		
		/**
		 * Assign a user story to a milestone id
		 *
		 * @param BUGSrequest $request
		 */
		public function runScrumAssignStory(BUGSrequest $request)
		{
			$this->forward403unless(BUGScontext::getUser()->hasPageAccess('project_scrum', $this->selected_project->getID()) || BUGScontext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			try
			{
				$issue = BUGSfactory::BUGSissueLab($request->getParameter('story_id'));
				$new_sprint_id = (int) $request->getParameter('sprint_id');
				$sprint = null;
				try
				{
					$sprint = BUGSfactory::milestoneLab($new_sprint_id);
				}
				catch (Exception $e) {}
				if ($issue instanceof BUGSissue)
				{
					$old_sprint = $issue->getMilestone();
					$issue->setMilestone($new_sprint_id);
					$issue->save();
					$old_sprint_id = ($old_sprint instanceof BUGSmilestone) ? $old_sprint->getID() : 0;
					$old_issues = ($old_sprint instanceof BUGSmilestone) ? $old_sprint->countIssues() : 0;
					$new_issues = ($sprint instanceof BUGSmilestone) ? $sprint->countIssues() : 0;
					$old_e_points = ($old_sprint instanceof BUGSmilestone) ? $old_sprint->getPointsEstimated() : 0;
					$new_e_points = ($sprint instanceof BUGSmilestone) ? $sprint->getPointsEstimated() : 0;
					$old_s_points = ($old_sprint instanceof BUGSmilestone) ? $old_sprint->getPointsSpent() : 0;
					$new_s_points = ($sprint instanceof BUGSmilestone) ? $sprint->getPointsSpent() : 0;
					$old_e_hours = ($old_sprint instanceof BUGSmilestone) ? $old_sprint->getHoursEstimated() : 0;
					$new_e_hours = ($sprint instanceof BUGSmilestone) ? $sprint->getHoursEstimated() : 0;
					$old_s_hours = ($old_sprint instanceof BUGSmilestone) ? $old_sprint->getHoursSpent() : 0;
					$new_s_hours = ($sprint instanceof BUGSmilestone) ? $sprint->getHoursSpent() : 0;
					return $this->renderJSON(array('failed' => false, 'issue_id' => $issue->getID(), 'old_sprint_id' => $old_sprint_id, 'old_issues' => $old_issues, 'old_estimated_points' => $old_e_points, 'old_spent_points' => $old_s_points, 'old_estimated_hours' => $old_e_hours, 'old_spent_hours' => $old_s_hours, 'new_sprint_id' => $new_sprint_id, 'new_issues' => $new_issues, 'new_estimated_points' => $new_e_points, 'new_spent_points' => $new_s_points, 'new_estimated_hours' => $new_e_hours, 'new_spent_hours' => $new_s_hours));
				}
				return $this->renderJSON(array('failed' => true, 'error' => BUGScontext::getI18n()->__('Invalid user story or sprint')));
			}
			catch (Exception $e)
			{
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}
		
		/**
		 * Add a new sprint type milestone to a project
		 *  
		 * @param BUGSrequest $request
		 */
		public function runScrumAddSprint(BUGSrequest $request)
		{
			$this->forward403unless(BUGScontext::getUser()->hasPageAccess('project_scrum', $this->selected_project->getID()) || BUGScontext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			if (($sprint_name = $request->getParameter('sprint_name')) && trim($sprint_name) != '')
			{
				$sprint = BUGSmilestone::createNew($sprint_name, BUGSmilestone::TYPE_SCRUMSPRINT, $this->selected_project->getID());
				return $this->renderJSON(array('failed' => false, 'content' => $this->getTemplateHTML('sprintbox', array('sprint' => $sprint)), 'sprint_id' => $sprint->getID()));
			}
			return $this->renderJSON(array('failed' => true, 'error' => BUGScontext::getI18n()->__('Please specify a sprint name')));
		}

		/**
		 * The project issue list page
		 *
		 * @param BUGSrequest $request
		 */
		public function runIssues(BUGSrequest $request)
		{
			$this->forward403unless(BUGScontext::getUser()->hasPageAccess('project_issues', $this->selected_project->getID()) || BUGScontext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
		}

		/**
		 * The project team page
		 *
		 * @param BUGSrequest $request
		 */
		public function runTeam(BUGSrequest $request)
		{
			$this->forward403unless(BUGScontext::getUser()->hasPageAccess('project_team', $this->selected_project->getID()) || BUGScontext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
		}

		/**
		 * The project statistics page
		 *
		 * @param BUGSrequest $request
		 */
		public function runStatistics(BUGSrequest $request)
		{
			$this->forward403unless(BUGScontext::getUser()->hasPageAccess('project_statistics', $this->selected_project->getID()) || BUGScontext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
		}

		/**
		 * Return the project menu strip
		 *
		 * @param BUGSrequest $request The request object
		 */
		public function runGetMenustrip(BUGSrequest $request)
		{
			$this->forward403unless($request->isMethod(BUGSrequest::POST) && $request->hasParameter('project_id'));
			$project = null;
			$hide_button = ($request->getParameter('page') == 'reportissue') ? true : false;
			$this->getResponse()->setPage($request->getParameter('page'));

			try
			{
				$project = BUGSfactory::projectLab($request->getParameter('project_id'));
			}
			catch (Exception $e) {}

			return $this->renderComponent('menustrip', array('project' => $project, 'hide_button' => $hide_button));
		}

	}