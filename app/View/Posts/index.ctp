<div class="posts index">
	<h2 style="padding: 10px 0">
		<?php echo __('Posts'); ?>
		<?php echo $this->Html->link(__('New Post'), array('action' => 'add'),array('class'=>'btn btn-md btn-success pull-right')); ?>
	</h2>
	<table class="table table-hover table-bordered">
	<tr>
			<th><?php echo $this->Paginator->sort('user_id'); ?></th>
			<th><?php echo $this->Paginator->sort('image'); ?></th>
			<th><?php echo $this->Paginator->sort('title'); ?></th>
			<th><?php echo $this->Paginator->sort('body'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($posts as $post): ?>
	<tr>
		<td>
			<?php echo $this->Html->link($post['User']['username'], array('controller' => 'users', 'action' => 'view', $post['User']['id'])); ?>
		</td>
		<td><?php echo h($post['Post']['image']); ?>&nbsp;</td>
		<td><?php echo h($post['Post']['title']); ?>&nbsp;</td>
		<td><?php echo h($post['Post']['body']); ?>&nbsp;</td>
		<td><?php echo h($post['Post']['created']); ?>&nbsp;</td>
		<td><?php echo h($post['Post']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $post['Post']['id']), array('class'=>'btn btn-sm btn-primary')); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $post['Post']['id']), array('class'=>'btn btn-sm btn-success')); ?>
			<?php 
				echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $post['Post']['id']), array('class'=>'btn btn-sm btn-danger'), __('Are you sure you want to delete # %s?', $post['Post']['id']));
			?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
