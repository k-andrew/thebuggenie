<div class="whiteboard-issue <?php if ($issue->isClosed()) echo 'issue_closed'; ?> <?php if ($issue->isBlocking()) echo 'blocking'; ?>" data-issue-id="<?php echo $issue->getID(); ?>" data-status-id="<?php echo $issue->getStatus()->getID(); ?>">
    <?php include_component('project/planningcolorpicker', array('issue' => $issue)); ?>
    <?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey())), $issue->getFormattedTitle(true, false), array('title' => $issue->getFormattedTitle(), 'target' => '_new', 'class' => 'issue_header')); ?>
    <div class="extra">
        <div class="description"><?php echo __e($issue->getDescription()); ?></div>
    </div>
    <?php if ($issue->hasChildIssues()): ?>
        <ol class="child-issues">
            <?php foreach ($issue->getChildIssues() as $child_issue): ?>
                <li title="<?php echo __e($child_issue->getFormattedTitle()); ?>"><?php echo __e($child_issue->getTitle()); ?></li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
    <div class="issue_info">
        <?php foreach ($issue->getBuilds() as $details): ?>
            <div class="issue_release"><?php echo $details['build']->getVersion(); ?></div>
        <?php endforeach; ?>
        <?php foreach ($issue->getComponents() as $details): ?>
            <div class="issue_component"><?php echo $details['component']->getName(); ?></div>
        <?php endforeach; ?>
        <?php if ($swimlane->getBoard()->getEpicIssuetypeID() && $issue->hasParentIssuetype($swimlane->getBoard()->getEpicIssuetypeID())): ?>
            <?php foreach ($issue->getParentIssues() as $parent): ?>
                <?php if ($parent->getIssueType()->getID() == $swimlane->getBoard()->getEpicIssuetypeID()): ?>
                    <div class="epic_badge" style="background-color: <?php echo $parent->getScrumColor(); ?>" data-parent-epic-id="<?php echo $parent->getID(); ?>"><?php echo $parent->getShortname(); ?></div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="issue_info">
        <?php if ($issue->getStatus() instanceof TBGDatatype): ?>
            <div class="status_badge" style="background-color: <?php echo ($issue->getStatus() instanceof TBGDatatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;" title="<?php echo ($issue->getStatus() instanceof TBGDatatype) ? $issue->getStatus()->getName() : __('Unknown'); ?>">&nbsp;&nbsp;&nbsp;</div>
        <?php endif; ?>
        <?php if ($issue->isAssigned()): ?>
            <?php if ($issue->getAssignee() instanceof TBGUser): ?>
                <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $issue->getAssignee()->getID())); ?>');"><?php echo image_tag($issue->getAssignee()->getAvatarURL(), array('alt' => ' ', 'class' => 'avatar'), true); ?></a>
            <?php else: ?>
                <?php echo include_component('main/teamdropdown', array('team' => $issue->getAssignee(), 'size' => 'large', 'displayname' => '')); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="issue_estimates">
        <div class="issue_estimate points" title="<?php echo __('Estimated points'); ?>" style="<?php if (!$issue->getEstimatedPoints()) echo 'display: none;'; ?>"><?php echo $issue->getEstimatedPoints(); ?></div>
        <div class="issue_estimate hours" title="<?php echo __('Estimated hours'); ?>" style="<?php if (!$issue->getEstimatedHours()) echo 'display: none;'; ?>"><?php echo $issue->getEstimatedHours(); ?></div>
    </div>
</div>