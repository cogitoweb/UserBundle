CREATE TABLE [dbo].[sessions] (
	[sess_id]       [nvarchar](255) NOT NULL,
	[sess_data]     [ntext]         NOT NULL,
	[sess_time]     [int]           NOT NULL,
	[sess_lifetime] [int]           NOT NULL,
	PRIMARY KEY CLUSTERED (
		[sess_id] ASC
	) WITH (
		PAD_INDEX              = OFF,
		STATISTICS_NORECOMPUTE = OFF,
		IGNORE_DUP_KEY         = OFF,
		ALLOW_ROW_LOCKS        = ON,
		ALLOW_PAGE_LOCKS       = ON
	) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]